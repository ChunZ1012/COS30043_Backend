<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
class ProductModel extends Database
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getProducts($limit, $withDesc)
    {
        $query = "SELECT v.variant_id AS variantId, t.variant_type_text AS variantType, v.variant_value AS variantTypeValue, v.variant_price AS variantPrice, v.variant_discount AS variantDiscount, v.variant_discount_amt AS variantDiscountAmt FROM (SELECT * FROM product_variants GROUP BY product_id) AS v INNER JOIN product_variants_type t ON v.variant_type = t.variant_type_id LIMIT ?;";

        $productQuery = "SELECT p.product_id AS productId, p.product_name AS productTitle, p.product_type AS productType, p.product_catg AS productCatg, IFNULL(AVG(user_rating), 0) AS productOverallRating";

        if(filter_var($withDesc, FILTER_VALIDATE_BOOLEAN)) $productQuery .= ", p.product_desc AS productDesc";
        $productQuery .= " FROM `products` p LEFT JOIN product_reviews r ON p.product_id = r.product_id GROUP BY p.product_id;";

        $variantQuery = "SELECT CONCAT('".PUBLIC_ASSETS_IMAGE_PATH."', SUBSTRING_INDEX(v.variant_image_urls, ',', 1)) AS productImageUrl, CONCAT('".PUBLIC_ASSETS_IMAGE_PATH."', SUBSTRING_INDEX(SUBSTRING_INDEX(v.variant_image_urls, ',', 2), ',', -1)) AS productImageHoverUrl, v.variant_price AS productPrice, v.variant_discount AS productDist, v.variant_discount_amt AS productDistAmt FROM product_variants v WHERE v.product_id = ? LIMIT 1;";

        $products = $this->select($productQuery);

        for($i = 0; $i < count($products); $i++)
        {
            $product = $products[$i];
            $variantInfo = $this->select($variantQuery, [
                'i',
                $product["productId"]
            ]);

            if(count($variantInfo) > 0) $products[$i] = array_merge($product, $variantInfo[0]);
        }
        return $products;
    }
    public function getProduct($productId)
    {
        $productQuery = "SELECT p.product_id AS productId, p.product_name AS productTitle, p.product_desc AS productDesc, IFNULL(AVG(r.user_rating), 0) AS productOverallRating FROM products p INNER JOIN product_reviews r ON p.product_id = r.product_id WHERE p.product_id = ? LIMIT 1;";
        $productInfo = $this->selectFirstRow($productQuery, ["i", $productId]);

        if($productInfo == null || !isset($productInfo['productId'])) throw new InvalidArgumentException("The product is not exist!");

        // $variantQuery = "SELECT v.variant_id AS variantId, t.variant_type_text AS variantType, v.variant_value AS variantTypeValue, v.variant_price AS variantPrice, v.variant_discount AS variantDiscount, v.variant_discount_amt AS variantDiscountAmt FROM product_variants v INNER JOIN product_variants_type t ON v.variant_type = t.variant_type_id WHERE v.product_id = ?";

        // $variantQuery = "SELECT v.variant_id as productVariantId, v.variant_image_urls AS productImageUrl, v.variant_value AS productVariantValue, v.variant_avail_qty AS productVariantAvailQty, v.variant_price AS productVariantPrice, v.variant_discount AS productVariantDist, v.variant_discount_amt AS productVariantDistAmt FROM product_variants v INNER JOIN product_variants_type t ON v.variant_type = t.variant_type_id WHERE v.product_id = ? AND v.variant_type = ? ORDER BY v.id ASC";

        // $variants = [];
        // foreach($variantTypes as $variantType)
        // {
        //     $variantsData = $this->select($variantQuery, 
        //         ["ii", $productId, $variantType['variant_type']]
        //     );
        //     array_push($variants, [
        //         "variantType" => $variantType["variant_type_text"],
        //         "variantInfo" => $variantsData
        //     ]);
        // }
        // $productInfo["productVariants"] = $variants;

        $variantTypeQuery = "SELECT DISTINCT p.variant_type as variantType, t.variant_type_text AS variantTypeText FROM product_variants p INNER JOIN product_variants_type t ON p.variant_type = t.variant_type_id WHERE product_id = ?;";

        $productInfo["productVariants"] = $this->select($variantTypeQuery, [
            'i',
            $productId
        ]);

        $variantInfoQuery = "SELECT v.variant_id as productVariantId, v.variant_image_urls AS productImageUrl, v.variant_type AS productVariantType, v.variant_value AS productVariantValue, v.variant_avail_qty AS productVariantAvailQty, v.variant_price AS productVariantPrice, v.variant_discount AS productVariantDist, v.variant_discount_amt AS productVariantDistAmt FROM product_variants v WHERE v.product_id = ? ORDER BY v.id ASC;";

        $variants = $this->select($variantInfoQuery, [
            'i',
            $productId
        ]);

        $productInfo['productVariantsInfo'] = $variants; 
        $reviewSql = "SELECT user_display_name AS userDisplayName, user_rating AS userRating, user_comment AS userComment, user_comment_time AS userCommentTime FROM `product_reviews` WHERE product_id = ?;";

        $productInfo["productReviews"] = $this->select($reviewSql, [
            'i',
            $productId
        ]);

        return $productInfo;
    }

    public function getProductVariantPriceInfo($variantId)
    {
        $sql = "SELECT variant_price, variant_discount, variant_discount_amt FROM `product_variants` WHERE variant_id = ? LIMIT 1;";
        return $this->selectFirstRow($sql, [
            'i',
            $variantId
        ]);
    }

    public function checkIfProductExist($productId) 
    {
        $sql = "SELECT COUNT(*) = 1 FROM products WHERE product_id = ?";
        return $this->execScalar($sql, [
            'i',
            $productId
        ]);
    }
    public function checkIfVariantExist($variantId) 
    {
        $sql = "SELECT COUNT(*) = 1 FROM product_variants WHERE variant_id = ?";
        return $this->execScalar($sql, [
            'i',
            $variantId
        ]);
    }
    public function checkVariantAvailQtyAgainstUserQty($variantId, $userQty)
    {
        $sql = "SELECT variant_avail_qty >= ? AND 1 <= ? AS isValid FROM product_variants WHERE variant_id = ?;";

        return $this->execScalar($sql, [
            'iii',
            $userQty,
            $userQty,
            $variantId
        ]);
    }
}