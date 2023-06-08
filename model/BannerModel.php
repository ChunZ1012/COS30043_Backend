<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class BannerModel extends Database 
{
    public function getBanners()
    {
        $sql = "SELECT CONCAT('".PUBLIC_ASSETS_BANNER_PATH."', banner) AS banner FROM banners ORDER BY id ASC;";
        
        return $this->select($sql);
    }
}