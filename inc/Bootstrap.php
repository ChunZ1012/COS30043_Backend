<?php
define("PROJECT_ROOT_PATH", __DIR__ . "/../");

$rootPathArray = explode("\\", dirname(__DIR__));
$subRootFolder = $rootPathArray[count($rootPathArray) - 1];
define("PUBLIC_ASSETS_IMAGE_PATH", "http://".$_SERVER['HTTP_HOST']."/".$subRootFolder."/public/assets/img/");

// include main configuration file 
require_once PROJECT_ROOT_PATH . "/inc/Config.php";
// include route configuration file
require_once PROJECT_ROOT_PATH . "/inc/Route.php";
// include the base controller file 
require_once PROJECT_ROOT_PATH . "/Controller/Api/BaseController.php";
// include the user model file 
require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";
// include the product model file 
require_once PROJECT_ROOT_PATH . "/Model/ProductModel.php";
?>