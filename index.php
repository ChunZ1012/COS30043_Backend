<?php
    require __DIR__ . "/inc/bootstrap.php";
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = explode( '/', $uri );

    $uriModel = isset($uri[3]) ? $uri[3] : null;
    // If the corresponding controller method is not found
    // Then return 404
    if($uriModel == null && !in_array($uriModel, $routes))
    {
        header("HTTP/1.1 404 Not Found");
        exit();
    }
    $route = $routes[$uriModel];
    // E.g Product
    $objControllerClassName = $route['class'].'Controller';
    // Import class
    require PROJECT_ROOT_PATH . "/Controller/Api/".$objControllerClassName.".php";

    // E.g new ProductController();
    $objController = new $objControllerClassName();

    // If the corresponding controller method is not found
    // Then return 404
    if(!isset($uri[3]) && !in_array($uri[3], $route['routes']))
    {
        header("HTTP/1.1 404 Not Found");
        exit();
    }
    $controllerMethodInfo = $route['routes'];
    if(isset($controllerMethodInfo[$uri[4]])) $methodName = $controllerMethodInfo[$uri[4]];
    else $methodName = $uri[4];
    $methodName .= "Action";

    $objController->{$methodName}();
/*
    if ((isset($uri[2]) && $uri[2] != 'user') || !isset($uri[3])) {
        header("HTTP/1.1 404 Not Found");
        exit();
    }

    require PROJECT_ROOT_PATH . "/Controller/Api/UserController.php";
    $objFeedController = new UserController();
    $strMethodName = $uri[3] . 'Action';
    $objFeedController->{$strMethodName}();
*/
?>