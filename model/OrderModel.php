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
    public function getOrders($userId, $limit)
    {
        $headerSql = "SELECT o.order_id AS id, o.order_guid AS orderId, o.order_created_at AS orderCreatedAt, o.order_delivery_name AS orderDeliveryName, o.order_delivery_address AS orderDeliveryAddress, o.order_delivery_address_2 AS orderDeliveryAddress2, o.order_delivery_contact AS orderDeliveryContact, o.order_delivery_email AS orderDeliveryEmail, SUM(d.order_price * d.order_qty) AS orderTotalAmount, SUM(d.order_discount_amt * d.order_qty) AS orderTotalDiscount, o.order_status AS orderStatus FROM orders o INNER JOIN orders_detail d ON o.order_id = d.order_id WHERE o.user_id = ? GROUP BY o.order_id ORDER BY order_created_at DESC LIMIT ?";

        $orders = $this->select($headerSql, [
            "ii",
            $userId,
            $limit
        ]);

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
        $headerSql = "SELECT o.order_id AS id, o.order_guid AS orderId, o.order_created_at AS orderCreatedAt, o.order_delivery_name AS orderDeliveryName, o.order_delivery_address AS orderDeliveryAddress, o.order_delivery_address_2 AS orderDeliveryAddress2, o.order_delivery_contact AS orderDeliveryContact, o.order_delivery_email AS orderDeliveryEmail, SUM(d.order_price * d.order_qty) AS orderTotalAmount, SUM(d.order_discount_amt * d.order_qty) AS orderTotalDiscount, o.order_status AS orderStatus FROM orders o INNER JOIN orders_detail d ON o.order_id = d.order_id WHERE o.order_guid = ? LIMIT 1;";
        $order = $this->selectFirstRow($headerSql, ["s", $orderGuid]);

        if($order == null || !isset($order['id'])) throw new InvalidArgumentException("The selected order is no longer exist!");

        // $detailSql = "SELECT d.order_product_variant_id AS productVariantId, v.product_name AS productName, v.variant_value AS productVariantValue, d.order_qty AS orderVariantQty, d.order_price AS orderVariantPrice, d.order_discount AS orderVariantDiscount, d.order_discount_amt AS orderVariantDiscountAmt FROM orders_detail d INNER JOIN (SELECT v.variant_id, v.variant_value, p.product_name FROM product_variants v INNER JOIN products p ON v.product_id = p.product_id) AS v ON d.order_product_variant_id = v.variant_id WHERE d.order_id = ?";

        $detailSql = "SELECT d.order_product_variant_id AS productVariantId, v.product_name AS productName, t.variant_type_text AS productVariantType, v.variant_value AS productVariantValue, CONCAT('".PUBLIC_ASSETS_IMAGE_PATH."', SUBSTRING_INDEX(v.variant_image_urls, ',', 1)) AS variantImageUrl, d.order_qty AS orderVariantQty, d.order_price AS orderVariantPrice, d.order_discount AS orderVariantDiscount, d.order_discount_amt AS orderVariantDiscountAmt FROM orders_detail d INNER JOIN (SELECT v.variant_id, v.variant_value, v.variant_type, v.variant_image_urls, p.product_name FROM product_variants v INNER JOIN products p ON v.product_id = p.product_id) AS v ON d.order_product_variant_id = v.variant_id INNER JOIN product_variants_type t ON v.variant_type = t.variant_type_id WHERE d.order_id = ?";

        $order['orderDetail'] = $this->select($detailSql, ['i', $order['id']]);
        // Remove id key from array;
        unset($order['id']);

        return $order;
    }
    public function getOrderDeliveryLog($orderGuid)
    {
        $orderDeliveryLogSql = "SELECT order_log_time AS orderLogDate, order_remark AS orderLogRemark FROM orders_delivery_log WHERE order_id = (SELECT order_id FROM orders WHERE order_guid = ? LIMIT 1) ORDER BY id ASC;";

        if(!$this->isOrderExist($orderGuid)) throw new InvalidArgumentException("The selected order is not exist!");

        return $this->select($orderDeliveryLogSql, [
            's',
            $orderGuid
        ]);
    }

    public function addOrder($user_id, $payload, $fromCart)
    {
        $orderId = $this->insertOrder($user_id);
        // throw error if the order id is null/ the order is not inserted into db
        if(is_null($orderId)) throw new Error("Error when creating the order!");
        // if the order is created from cart
        if($fromCart)
        {
            foreach($payload as $p)
            {
                $isCartExist = $this->cartModel->checkIfCartExist($p['cartId'], $user_id);
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
            // save order detail to db
            $this->insertOrderDetail($orderId, $payload);
        }

        return $this->getOrderGuid($orderId);
    }
    public function checkout($orderGuid, $user_id, $payload)
    {
        $orderSql = "UPDATE orders SET order_delivery_name = ?, order_delivery_address = ?, order_delivery_address_2 = ?, order_delivery_contact = ?, order_delivery_email = ?, order_status = 1 WHERE order_guid = ? AND user_id = ?;";        
        // throw if there is no order exist
        if(!$this->isOrderExist($orderGuid)) throw new InvalidArgumentException('The selected order is not exist!');

        $r = $this->update($orderSql, [
            'ssssssi',
            $payload['orderDeliveryName'],
            $payload['orderDeliveryAddress1'],
            $payload['orderDeliveryAddress2'],
            $payload['orderDeliveryContact'],
            $payload['orderDeliveryEmail'],
            $orderGuid,
            $user_id
        ]);

        $productQtySql = "UPDATE product_variants SET variant_avail_qty = variant_avail_qty - ? WHERE variant_id = ?";
        $orderDetailSql = "SELECT order_product_variant_id AS variantId, order_qty AS qty FROM `orders_detail` WHERE order_id = (SELECT order_id FROM orders WHERE order_guid = ?)";
        // Get order products detail
        $orderDetails = $this->select($orderDetailSql, [
            's',
            $orderGuid
        ]);

        foreach($orderDetails as $d)
        {
            // update available qty for variant
            $this->update($productQtySql, [
                'ii',
                $d['qty'],
                $d['variantId']
            ]);
        }

        return $r;
    }
    public function cancelOrder($orderGuid, $reason)
    {
        $sql = "UPDATE orders SET order_is_cancelled = 1, order_cancelled_reason = ? WHERE order_guid = ?";
        return $this->update($sql, [
            'ss',
            $reason,
            $orderGuid
        ]);
    }
    public function deleteOrder($orderGuid)
    {
        $orderSql = "DELETE FROM orders WHERE order_guid = ? AND order_status = 1";
        if(!$this->isOrderExist($orderGuid)) throw new InvalidArgumentException('The selected order is not exist!');
        if(!$this->checkIfOrderInPending($orderGuid)) throw new InvalidArgumentException("The selected order cannot be deleted!");

        return $this->delete($orderSql, [
            's',
            $orderGuid
        ]);
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
    public function checkIfOrderInPending($orderGuid) : bool
    {
        $sql = "SELECT order_status = 0 AS IsPending FROM orders WHERE order_guid = ?;";
        return $this->execScalar($sql, [
            's',
            $orderGuid
        ]);
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
    private function insertOrder($user_id)
    {
        $orderSql = "INSERT INTO orders (user_id, order_delivery_name, order_delivery_address, order_delivery_contact, order_delivery_email) VALUES(?, ?, ?, ?, ?);";
        $orderIdSql = "SELECT order_id FROM orders WHERE id = ?";

         // Insert order into the database
        $insertedId = $this->insert($orderSql, [
            'issss',
            $user_id, 
            '', '', '', ''
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
            'iiidid',
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