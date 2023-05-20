<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
class ProductCategoryModel extends Database
{
    public function getProductCategories()
    {
        $sql = "SELECT DISTINCT product_catg AS productCatg FROM products";

        return $this->select($sql);
    }
}