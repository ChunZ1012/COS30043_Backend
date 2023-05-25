<?php
require_once PROJECT_ROOT_PATH . "/Model/ProductModel.php";
class ProductController extends BaseController 
{
    private $model;
    public function __construct()
    {
        $this->model = new ProductModel();
    }
    public function listAction() 
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $intLimit = isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit'] ? $arrQueryStringParams['limit'] : 10;
                $withDesc = isset($arrQueryStringParams['desc']) && $arrQueryStringParams['desc'] ? $arrQueryStringParams['desc'] : false;

                $products = $this->model->getProducts($intLimit, $withDesc);
                $responseData = json_encode($products);
            } catch (Error $e) {
                $strErrorDesc = 'Something went wrong!';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        $this->handleOutput($responseData, $strErrorDesc, $strErrorHeader);
    }
    public function getAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();        

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $productId = isset($arrQueryStringParams['id']) && $arrQueryStringParams['id'] ? $arrQueryStringParams['id'] : -1;

                if(!$this->model->checkIfProductExist($productId)) throw new InvalidArgumentException("The product is not exist!");

                $products = $this->model->getProduct($productId);
                $responseData = json_encode($products);
            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            } catch (Error $e) {
                $strErrorDesc = 'Something went wrong!';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        $this->handleOutput($responseData ?? '', $strErrorDesc, $strErrorHeader);
    }
}