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
       
        if (strtoupper($requestMethod) == 'POST') {
            try {
                $intLimit = $this->getSpecificQueryStringParam('limit') ?? 10;
                // Get order list with limit
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
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
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
    public function detailAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];   

        if (strtoupper($requestMethod) == 'POST') {
            try {
                // TODO: uncomment below code
                // Only if user is logged in
                // if(isset($_SESSION['userId']) && $_SESSION['userId'])
                if(true)
                {
                    $orderGuid = $this->getSpecificQueryStringParam('id');
                    if($orderGuid == null) throw new InvalidArgumentException('Invalid request! Missing id field');

                    $order = $this->model->getOrderByGuid($orderGuid);
                    $order['orderLogs'] =  $this->model->getOrderDeliveryLog($orderGuid);
                    $responseData = json_encode($order);
                }
                else throw new InvalidArgumentException("Please login before continue!");
            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
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
    public function checkAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == 'POST') {
            try {
                // Only if user is logged in
                // if(isset($_SESSION['userId']) && $_SESSION['userId'])
                if(true)
                {
                    $orderGuid = $this->getSpecificQueryStringParam('id');
                    // If the order guid is null then throw
                    if($orderGuid == null) throw new InvalidArgumentException('The selected order is not exist!');
                    // If the order is not in pending status then throw
                    if(!$this->model->checkIfOrderInPending($orderGuid)) throw new InvalidArgumentException('The selected order is no longer exist!');
                    $order = $this->model->getOrderByGuid($orderGuid);
                    $responseData = json_encode($order);
                }
                else throw new InvalidArgumentException("Please login before continue!");
            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
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
    public function checkoutAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $formKeys = [
            'orderDeliveryName',
            'orderDeliveryAddress',
            'orderDeliveryAddress2',
            'orderDeliveryContact',
            'orderDeliveryEmail'
        ];

        if (strtoupper($requestMethod) == 'POST') {
            try {
                $postData = $this->getPostData();
                // Flip formKeys and test with $postData
                $diff = array_diff_key(array_flip($formKeys), $postData);
                // throw if both keys are not equal, eg, one of the form fields is missing
                if(count($diff) > 0) throw new InvalidArgumentException("Please fill in all of the required form fields!");

                $orderGuid = $this->getSpecificQueryStringParam('id');
                if($orderGuid == null) throw new InvalidArgumentException('Invalid request! Missing id field');
                // checkout
                $r = $this->model->checkout($orderGuid, $postData);
                echo $r;
                if(!$r) throw new Error();
                else $responseData = true;

            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
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
    public function deleteAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == 'DELETE') {
            try {
                $orderGuid = $this->getSpecificQueryStringParam('id');
                if($orderGuid == null) throw new InvalidArgumentException('Invalid request! Missing id field');
                // Delete order
                $responseData = $this->model->deleteOrder($orderGuid);
            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
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