<?php
require PROJECT_ROOT_PATH . "/Model/OrderModel.php";
require PROJECT_ROOT_PATH . "/Model/CartModel.php";
class OrderController extends BaseController 
{
    private $model;
    private $cartModel;
    public function __construct()
    {
        $this->model = new OrderModel();
        $this->cartModel = new CartModel();
    }
    public function listAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        
        if (strtoupper($requestMethod) == 'POST') {
            try {
                $intLimit = 10;
                if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                    $intLimit = $arrQueryStringParams['limit'];
                }
                $orders = $this->model->getOrders($intLimit);
                $responseData = json_encode($orders);
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong!';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        $this->handleOutput($responseData ?? '', $strErrorDesc, $strErrorHeader);
    }
    public function getAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();        

        if (strtoupper($requestMethod) == 'POST') {
            try {
                // TODO: uncomment below code
                // Only if user is logged in
                // if(isset($_SESSION['userId']) && $_SESSION['userId'])
                if(true)
                {
                    $orderGuid = isset($arrQueryStringParams['id']) && $arrQueryStringParams['id'] ? $arrQueryStringParams['id'] : -1;

                    $order = $this->model->getOrderByGuid($orderGuid);
                    $responseData = json_encode($order);
                }
                else throw new InvalidArgumentException("Please login before continue!");
            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong!';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        $this->handleOutput($responseData ?? '', $strErrorDesc, $strErrorHeader);
    }
    public function addAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $responseData = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];    

        if (strtoupper($requestMethod) == 'POST') {
            try {
                $orderData = $this->getPostData();
                // If the user initiate this request from cart page
                if(isset($orderData['fromCart']) && $orderData['fromCart'])
                {
                    $products = $orderData["data"];
                    // passing product data to model
                    $orderGuid = $this->model->addOrder($products, true);
                    // set error prop to false, and return order guid if there is no exception thrown by model
                    $result['error'] = false;
                    $result['orderGuid'] = $orderGuid;
                }
                // The user initiate this request in Product Detail page via Buy now
                else 
                {
                    // print_r($orderData);
                    // add order data
                    $orderGuid = $this->model->addOrder($orderData, false);
                    // set error prop to false, and return order guid if there is no exception thrown by model
                    $result['error'] = false;
                    $result['orderGuid'] = $orderGuid;
                }

                $responseData = json_encode($result);
            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'\nSomething went wrong!';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }

        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        $this->handleOutput($responseData ?? '', $strErrorDesc, $strErrorHeader);
    }

    private function getDeliveryInfo()
    {
        $postData = $this->getPostData();

        return [
            'deliveryName' => $postData['delivery_name'],
            'deliveryAddress' => $postData['delivery_address'],
            'deliveryContact' => $postData['delivery_contact'],
            'deliveryEmail' => $postData['delivery_email'],
        ];
    }
}