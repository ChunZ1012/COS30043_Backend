<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
class OrderModel extends Database 
{
    private $productModel;
    private $cartModel;
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel();
        $this->cartModel = new CartModel();
    }
    public function getOrders($limit)
    {
        $headerSql = "SELECT o.order_id AS id, o.order_guid AS orderId, o.order_created_at AS orderCreatedAt, o.order_delivery_name AS orderDeliveryName, o.order_delivery_address AS orderDeliveryAddress, o.order_delivery_address_2 AS orderDeliveryAddress2, o.order_delivery_contact AS orderDeliveryContact, o.order_delivery_email AS orderDeliveryEmail, SUM(d.order_price * d.order_qty) AS orderTotalAmount, SUM(d.order_discount_amt * d.order_qty) AS orderTotalDiscount FROM orders o INNER JOIN orders_detail d ON o.order_id = d.order_id WHERE o.user_id = ? LIMIT ?";
        $orders = $this->select($headerSql, ["ii", 1, $limit]);

        $detailSql = "SELECT d.order_product_variant_id AS productVariantId, v.product_name AS productName, t.variant_type_text AS productVariantType, v.variant_value AS productVariantValue, CONCAT('".PUBLIC_ASSETS_IMAGE_PATH."', SUBSTRING_INDEX(v.variant_image_urls, ',', 1)) AS variantImageUrl, d.order_qty AS orderVariantQty, d.order_price AS orderVariantPrice, d.order_discount AS orderVariantDiscount, d.order_discount_amt AS orderVariantDiscountAmt FROM orders_detail d INNER JOIN (SELECT v.variant_id, v.variant_value, v.variant_type, v.variant_image_urls, p.product_name FROM product_variants v INNER JOIN products p ON v.product_id = p.product_id) AS v ON d.order_product_variant_id = v.variant_id INNER JOIN product_variants_type t ON v.variant_type = t.variant_type_id WHERE d.order_id = ?";
    
        for($i = 0; $i < count($orders); $i++)
        {
            $orderDetail = $this->select($detailSql, ["i", $orders[$i]['id']]);
            $orders[$i]['orderDetail'] = $orderDetail;
        }

        return $orders;
    }
    public function getOrderByGuid($orderGuid)
    {
        $headerSql = "SELECT o.order_id AS id, o.order_guid AS orderId, o.order_created_at AS orderCreatedAt, o.order_delivery_name AS orderDeliveryName, o.order_delivery_address AS orderDeliveryAddress, o.order_delivery_address_2 AS orderDeliveryAddress2, o.order_delivery_contact AS orderDeliveryContact, o.order_delivery_email AS orderDeliveryEmail, SUM(d.order_price * d.order_qty) AS orderTotalAmount, SUM(d.order_discount_amt * d.order_qty) AS orderTotalDiscount FROM orders o INNER JOIN orders_detail d ON o.order_id = d.order_id WHERE o.order_guid = ? LIMIT 1;";
        $order = $this->selectFirstRow($headerSql, ["i", $orderGuid]);

        if($order == null || !isset($order['id'])) throw new InvalidArgumentException("The selected order is no longer exist!");

        // $detailSql = "SELECT d.order_product_variant_id AS productVariantId, v.product_name AS productName, v.variant_value AS productVariantValue, d.order_qty AS orderVariantQty, d.order_price AS orderVariantPrice, d.order_discount AS orderVariantDiscount, d.order_discount_amt AS orderVariantDiscountAmt FROM orders_detail d INNER JOIN (SELECT v.variant_id, v.variant_value, p.product_name FROM product_variants v INNER JOIN products p ON v.product_id = p.product_id) AS v ON d.order_product_variant_id = v.variant_id WHERE d.order_id = ?";

        $detailSql = "SELECT d.order_product_variant_id AS productVariantId, v.product_name AS productName, t.variant_type_text AS productVariantType, v.variant_value AS productVariantValue, CONCAT('".PUBLIC_ASSETS_IMAGE_PATH."', SUBSTRING_INDEX(v.variant_image_urls, ',', 1)) AS variantImageUrl, d.order_qty AS orderVariantQty, d.order_price AS orderVariantPrice, d.order_discount AS orderVariantDiscount, d.order_discount_amt AS orderVariantDiscountAmt FROM orders_detail d INNER JOIN (SELECT v.variant_id, v.variant_value, v.variant_type, v.variant_image_urls, p.product_name FROM product_variants v INNER JOIN products p ON v.product_id = p.product_id) AS v ON d.order_product_variant_id = v.variant_id INNER JOIN product_variants_type t ON v.variant_type = t.variant_type_id WHERE d.order_id = ?";

        $order['orderDetail'] = $this->select($detailSql, ['i', $order['id']]);

        return $order;
    }

    public function addOrder($payload, $fromCart)
    {
        $orderId = $this->insertOrder();
        // throw error if the order id is null/ the order is not inserted into db
        if(is_null($orderId)) throw new Error("Error when creating the order!");
        // if the order is created from cart
        if($fromCart)
        {
            foreach($payload as $p)
            {
                // TODO: Get user id from session
                $isCartExist = $this->cartModel->checkIfCartExist($p['cartId'], 1);
                if(!$isCartExist) throw new InvalidArgumentException("The selected cart is no longer exist!");
                $variantId = $p['variantId'];
                $this->checkIfVariantExistOrThrow($variantId);
                $this->checkIfOrderQtyValidOrThrow($variantId, $p['variantOrderedQty']);

                $r = $this->insertOrderDetail($orderId, $p);
                if(!$r) throw new Error("Error when inserting order data!");
            }
        }
        else
        {
            $variantId = $payload['variantId'];
            $this->checkIfVariantExistOrThrow($variantId);
            $this->checkIfOrderQtyValidOrThrow($variantId, $payload['variantOrderedQty']);

            $this->insertOrderDetail($orderId, $payload);
        }

        return $this->getOrderGuid($orderId);
    }

    public function isOrderExist($orderGuid)
    {
        $sql = "SELECT COUNT(order_id) > 0 AS IsExist FROM orders WHERE order_guid = ?";
        $result = $this->execScalar($sql, [
            's',
            $orderGuid
        ]);

        return $result;
    }
    private function checkIfVariantExistOrThrow($variantId)
    {
        $isVariantExist = $this->productModel->checkIfVariantExist($variantId);
        echo 'is variant exist: '.$isVariantExist == 1;
        if(!$isVariantExist) throw new InvalidArgumentException("The selected product variant is no longer exist!");
    }
    private function checkIfOrderQtyValidOrThrow($variantId, $qty)
    {
        if(!$this->productModel->checkVariantAvailQtyAgainstUserQty($variantId, $qty)) throw new InvalidArgumentException('The ordered qty is either more than available qty or less than 1!');
    }
    private function insertOrder()
    {
        $orderSql = "INSERT INTO orders (user_id, order_delivery_name, order_delivery_address, order_delivery_contact, order_delivery_email) VALUES(?, ?, ?, ?, ?);";
        $orderIdSql = "SELECT order_id FROM orders WHERE id = ?";

         // Insert order into the database
        // TODO: Get user id from session
        $insertedId = $this->insert($orderSql, [
            'issss',
            1, '', '', '', ''
        ]);
        // get the inserted order guid
        $orderId = $this->execScalar($orderIdSql, [
            'i',
            $insertedId
        ]);

        return $orderId;
    }
    private function insertOrderDetail($orderId, $p)
    {
        $orderDetailSql = "INSERT INTO orders_detail (order_id, order_product_variant_id, order_qty, order_price, order_discount, order_discount_amt) VALUES(?, ?, ?, ?, ?, ?);";
        // Get product variant info
        $product = $this->productModel->getProductVariantPriceInfo($p['variantId']);
        return $this->insert($orderDetailSql, [
            'iiidbd',
            $orderId,
            $p["variantId"],
            $p["variantOrderedQty"],
            $product['variant_price'],
            $product['variant_discount'],
            $product['variant_discount_amt'],
        ]);
    }
    private function getOrderGuid($orderId)
    {
        $sql = "SELECT order_guid FROM orders WHERE order_id = ?";
        return $this->execScalar($sql, [
            'i',
            $orderId
        ]);
    }
}