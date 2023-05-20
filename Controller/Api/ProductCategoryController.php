<?php
require PROJECT_ROOT_PATH . "/Model/ProductCategoryModel.php";
class ProductCategoryController extends BaseController 
{
    private $model;
    public function __construct()
    {
        $this->model = new ProductCategoryModel();
    }
    public function listAction() 
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $catgs = $this->model->getProductCategories();
                $responseData = json_encode($catgs);
            } catch (Error $e) {
                $strErrorDesc = 'Something went wrong!';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        $this->handleOutput($responseData, $strErrorDesc, $strErrorHeader);
        $this->handleOutput($responseData, $strErrorDesc, $strErrorHeader);
    }
}