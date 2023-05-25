<?php
define("PROJECT_ROOT_PATH", __DIR__ . "/../");

$rootPathArray = explode("\\", dirname(__DIR__));
$subRootFolder = $rootPathArray[count($rootPathArray) - 1];
define("PUBLIC_ASSETS_IMAGE_PATH", "http://".$_SERVER['HTTP_HOST']."/".$subRootFolder."/public/assets/img/");

// include main session file 
require_once PROJECT_ROOT_PATH . "/inc/Session.php";
// include main configuration file 
require_once PROJECT_ROOT_PATH . "/inc/Config.php";
// include route configuration file
require_once PROJECT_ROOT_PATH . "/inc/Route.php";
// include the base controller file 
require_once PROJECT_ROOT_PATH . "/Controller/Api/BaseController.php";
// include vendor file
require_once PROJECT_ROOT_PATH . "/vendor/autoload.php";
?>