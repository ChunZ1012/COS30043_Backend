<?php
require PROJECT_ROOT_PATH . "/Model/CartModel.php";
class CartController extends BaseController 
{
    private $model;
    public function __construct()
    {
        $this->model = new CartModel();
    }
    public function listAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if ($requestMethod === 'GET') {
            try {
                $intLimit = $this->getSpecificQueryStringParam('limit') ?? 10;
                // TODO: Read $_SESSION object to retrieve user id
                $carts = $this->model->getCarts(1, $intLimit);
                $responseData = json_encode($carts);
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
    public function addAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $payload = [
                    'productVariantId' => $this->getSpecificQueryStringParam('variant_id') ?? -1,
                    'productVariantQty' => $this->getSpecificQueryStringParam('variant_qty') ?? -1
                ];
                if($payload['productVariantId'] == -1 || $payload['productVariantQty'] == -1) throw new InvalidArgumentException('The variant id and variant qty value must be valid!');
                // TODO: Read $_SESSION object to retrieve user id
                $carts = $this->model->addToCart($payload);
                $responseData = json_encode($carts);
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
                $payload = [
                    'cartId' => $this->getSpecificQueryStringParam('cart_id') ?? -1,
                    'productVariantId' => $this->getSpecificQueryStringParam('variant_id') ?? -1,
                    'productVariantQty' => $this->getSpecificQueryStringParam('variant_qty') ?? -1
                ];
                if($payload['cartId'] == -1 || $payload['productVariantId'] == -1 || $payload['productVariantQty'] == -1) throw new InvalidArgumentException('The cart id, variant id or variant qty value must be valid!');
                // TODO: Read $_SESSION object to retrieve user id
                $carts = $this->model->editCart($payload);
                $responseData = json_encode($carts);
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
                $payload = [
                    'cartId' => $this->getSpecificQueryStringParam('cart_id') ?? -1
                ];
                if($payload['cartId'] == -1) throw new InvalidArgumentException('The cart id, variant id or variant qty value must be valid!');
                // TODO: Read $_SESSION object to retrieve user id
                $carts = $this->model->removeFromCart($payload);
                $responseData = json_encode($carts);
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