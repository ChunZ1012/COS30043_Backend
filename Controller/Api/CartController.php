<?php
require_once PROJECT_ROOT_PATH . "/Model/CartModel.php";
require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";
require_once PROJECT_ROOT_PATH . "/Model/ProductModel.php";
class CartController extends BaseController 
{
    private $model;
    private $userModel;
    private $token;
    public function __construct()
    {
        $this->model = new CartModel();
        $this->userModel = new UserModel();

        $this->token = $this->getRequestToken();
    }
    public function listAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if ($requestMethod === 'POST') {
            try {
                if($this->userModel->isUserLoggedIn($this->token))
                {
                    $intLimit = $this->getSpecificQueryStringParam('limit') ?? 10;
                    $carts = $this->model->getCarts($this->userModel->getUserIdFromToken($this->token), $intLimit);
                    $responseData = json_encode($carts);
                }
                else throw new InvalidArgumentException("Please login before continue!");
            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
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
    public function addAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == 'POST') {
            try {
                if($this->userModel->isUserLoggedIn($this->token))
                {
                    $payload = [
                        'productVariantId' => $this->getSpecificQueryStringParam('variant_id') ?? -1,
                        'productVariantQty' => $this->getSpecificQueryStringParam('variant_qty') ?? -1
                    ];
                    if($payload['productVariantId'] == -1 || $payload['productVariantQty'] == -1) throw new InvalidArgumentException('The variant id and variant qty value must be valid!');

                    $carts = $this->model->addToCart($this->userModel->getUserIdFromToken($this->token), $payload);
                    $responseData = json_encode($carts);   
                }
                else throw new InvalidArgumentException("Please login before continue!");
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
    public function editAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == 'PATCH') {
            try {
                if($this->userModel->isUserLoggedIn($this->token))
                {
                    $payload = [
                        'cartId' => $this->getSpecificQueryStringParam('cart_id') ?? -1,
                        'productVariantId' => $this->getSpecificQueryStringParam('variant_id') ?? -1,
                        'productVariantQty' => $this->getSpecificQueryStringParam('variant_qty') ?? -1
                    ];
                    if($payload['cartId'] == -1 || $payload['productVariantId'] == -1 || $payload['productVariantQty'] == -1) throw new InvalidArgumentException('The cart id, variant id or variant qty value must be valid!');
                    $carts = $this->model->editCart($this->userModel->getUserIdFromToken($this->token), $payload);
                    $responseData = json_encode($carts);
                }
                else throw new InvalidArgumentException("Please login before continue!");
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

    public function deleteAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == "DELETE") {
            try {
                if($this->userModel->isUserLoggedIn($this->token))
                {
                    $payload = [
                        'cartId' => $this->getSpecificQueryStringParam('cart_id') ?? -1
                    ];
                    if($payload['cartId'] == -1) throw new InvalidArgumentException('The cart id, variant id or variant qty value must be valid!');

                    $carts = $this->model->removeFromCart($this->userModel->getUserIdFromToken($this->token), $payload);
                    $responseData = json_encode($carts);
                }
                else throw new InvalidArgumentException("Please login before continue!");
            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            } catch (Error | Exception $e) {
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