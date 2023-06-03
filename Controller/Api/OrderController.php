<?php
require_once PROJECT_ROOT_PATH . "/Model/OrderModel.php";
require_once PROJECT_ROOT_PATH . "/Model/CartModel.php";
require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";
require_once PROJECT_ROOT_PATH . "/Model/ProductModel.php";
class OrderController extends BaseController 
{
    private $model;
    private $cartModel;
    private $userModel;
    private $token;
    public function __construct()
    {
        $this->model = new OrderModel();
        $this->cartModel = new CartModel();
        $this->userModel = new UserModel();

        $this->token = $this->getRequestToken();
    }
    public function listAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == 'POST') {
            try {
                if($this->userModel->isUserLoggedIn($this->token))
                {
                    $intLimit = $this->getSpecificQueryStringParam('limit') ?? 10;
                    // Get order list with limit
                    $orders = $this->model->getOrders($this->userModel->getUserIdFromToken($this->token), $intLimit);
                    $responseData = json_encode($orders);
                }
                else throw new InvalidArgumentException("Please login before continue!");
                // else throw new InvalidArgumentException($this->token);
            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            } catch (Exception $e) {
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
    public function getAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];     

        if (strtoupper($requestMethod) == 'POST') {
            try {
                // Only if user is logged in
                if($this->userModel->isUserLoggedIn($this->token))
                {
                    $orderGuid = $this->getSpecificQueryStringParam('id') ?? -1;
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
                // Only if user is logged in
                if($this->userModel->isUserLoggedIn($this->token))
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
                if($this->userModel->isUserLoggedIn($this->token))
                {
                    $orderData = $this->getPostData();
                    // If the user initiate this request from cart page
                    if(isset($orderData['fromCart']) && $orderData['fromCart'])
                    {
                        $products = $orderData["data"];
                        // passing product data to model
                        $orderGuid = $this->model->addOrder($this->userModel->getUserIdFromToken($this->token), $products, true);
                        // set error prop to false, and return order guid if there is no exception thrown by model
                        $result['error'] = false;
                        $result['orderGuid'] = $orderGuid;
                    }
                    // The user initiate this request in Product Detail page via Buy now
                    else 
                    {
                        // print_r($orderData);
                        // add order data
                        $orderGuid = $this->model->addOrder($this->userModel->getUserIdFromToken($this->token), $orderData['data'], false);
                        // set error prop to false, and return order guid if there is no exception thrown by model
                        $result['error'] = false;
                        $result['orderGuid'] = $orderGuid;
                    }
    
                    $responseData = json_encode($result);
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
    public function cancelAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == 'PUT') {
            try {
                // Only if user is logged in
                if($this->userModel->isUserLoggedIn($this->token))
                {
                    $orderPost = $this->getPostData();
                    $orderGuid = isset($orderPost['orderId']) && $orderPost['orderId'] ? $orderPost['orderId'] : null;

                    if(!$this->model->isOrderExist($orderGuid)) throw new InvalidArgumentException('The selected order is not exist!');

                    if(!$this->model->checkIfOrderInPending($orderGuid)) throw new InvalidArgumentException('The selected order cannot be cancelled!');

                    $r = $this->model->cancelOrder($orderGuid, $orderPost['reason']);
                    $responseData = $r > 0;
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
    public function checkAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == 'POST') {
            try {
                // Only if user is logged in
                if($this->userModel->isUserLoggedIn($this->token))
                {
                    $orderGuid = $this->getSpecificQueryStringParam('id') ?? -1;
                    // If the order guid is null then throw
                    if($orderGuid == -1) throw new InvalidArgumentException('The selected order is not exist!');
                    // If the order is not in pending status then throw
                    if(!$this->model->isOrderExist($orderGuid) || !$this->model->checkIfOrderInPending($orderGuid)) throw new InvalidArgumentException('The selected order is no longer exist!');
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
            'orderDeliveryAddress1',
            'orderDeliveryAddress2',
            'orderDeliveryContact',
            'orderDeliveryEmail'
        ];

        if (strtoupper($requestMethod) == 'POST') {
            try {
               if($this->userModel->isUserLoggedIn($this->token))
               {
                    $postData = $this->getPostData();
                    // Flip formKeys and test with $postData
                    $diff = array_diff_key(array_flip($formKeys), $postData);
                    // throw if both keys are not equal, eg, one of the form fields is missing
                    if(count($diff) > 0) throw new InvalidArgumentException("Please fill in all of the required form fields!");

                    $orderGuid = $this->getSpecificQueryStringParam('id');
                    if($orderGuid == null) throw new InvalidArgumentException('Invalid request! Missing id field');
                    // checkout
                    $r = $this->model->checkout($orderGuid, $this->userModel->getUserIdFromToken($this->token), $postData);

                    if(!$r) throw new Error();
                    else $responseData = true;
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
    public function deleteAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == 'DELETE') {
            try {
                if($this->userModel->isUserLoggedIn($this->token))
                {
                    $orderGuid = $this->getSpecificQueryStringParam('id');
                    if($orderGuid == null) throw new InvalidArgumentException('Invalid request! Missing id field');
                    // Delete order
                    $responseData = $this->model->deleteOrder($orderGuid);
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
}