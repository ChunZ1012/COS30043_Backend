<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
class CartModel extends Database
{
    private $productModel;
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel();
    }
    public function getCarts($userId, $limit)
    {
        $sql = "SELECT c.cart_id AS cartId, p.product_id AS productId, p.product_name AS productName, v.variant_id AS variantId, t.variant_type_text AS variantTypeText, v.variant_value AS variantTypeValue, CONCAT('".PUBLIC_ASSETS_IMAGE_PATH."', SUBSTRING_INDEX(v.variant_image_urls, ',', 1)) AS variantImageUrl, c.product_variant_qty AS variantOrderedQty, v.variant_avail_qty AS variantAvailQty, v.variant_price AS variantPrice, v.variant_discount AS variantDist, v.variant_discount_amt AS variantDistAmt FROM carts c INNER JOIN product_variants v ON c.product_variant_id = v.variant_id INNER JOIN products p ON v.product_id = p.product_id INNER JOIN product_variants_type t ON v.variant_type = t.variant_type_id WHERE c.user_id = ? ORDER BY cartId DESC LIMIT ?";

        return $this->select($sql, ['ii', $userId, $limit]);
    }
    public function addToCart($payload)
    {
        $sql = "INSERT INTO carts (user_id, product_variant_id, product_variant_qty) VALUES (?, ?, ?);";

        if($payload)
        {
            if($this->productModel->checkVariantAvailQtyAgainstUserQty($payload['productVariantId'], $payload['productVariantQty']))
            {
                $result = $this->insert($sql, [
                    'iii',
                    // TODO: Get user id from session data,
                    1,
                    $payload['productVariantId'],
                    $payload['productVariantQty']
                ]);
    
                return $result > 0;
            }
            else throw new InvalidArgumentException("The qty is more than available qty of this variant!");
        }
        else throw new InvalidArgumentException("Empty payload is not supported!");
    }
    public function editCart($payload)
    {
        $sql = "UPDATE carts SET product_variant_qty = ? WHERE cart_id = ? AND product_variant_id = ? AND user_id = ?;";

        if($payload)
        {
            if($this->productModel->checkVariantAvailQtyAgainstUserQty($payload['productVariantId'], $payload['productVariantQty']))
            {
                $result = $this->update($sql, [
                    'iiii',
                    $payload['productVariantQty'],
                    $payload['cartId'],
                    $payload['productVariantId'],
                    // TODO: Get user id from session data,
                    1,
                ]);

                return $result > 0;
            }
            else throw new InvalidArgumentException("The qty is more than available qty of this variant!");
        }
        else throw new InvalidArgumentException("Empty payload is not supported!");
    }
    public function removeFromCart($payload)
    {
        $sql = "DELETE FROM carts WHERE cart_id = ? AND user_id = ?;";

        if($payload && isset($payload['cartId']))
        {
            $result = $this->delete($sql, [
                'ii',
                $payload['cartId'],
                // TODO: Get user id from session data,
                1,
            ]);
            return $result > 0;
        }
        else throw new InvalidArgumentException("Empty payload is not supported!");
    }
    public function checkIfCartExist($cartId, $userId)
    {
        $sql = "SELECT COUNT(cart_id) > 0 AS IsExist FROM carts WHERE cart_id = ? AND user_id = ?;";

        return $this->execScalar($sql, [
            'ii',
            $cartId,
            $userId
        ]);
    }

}