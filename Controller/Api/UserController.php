<?php
require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";
class UserController extends BaseController
{
    private $model;
    private $token;
    public function __construct()
    {
        $this->model = new UserModel();
        $this->token = $this->getRequestToken();
    }
    public function getAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if ($requestMethod === 'POST') {
            try {
                if($this->model->isUserLoggedIn($this->token))
                {
                    $responseData = json_encode($this->model->getUserAccountInfo($this->model->getUserIdFromToken($this->token)));
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
    public function updateProfileAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if ($requestMethod === 'POST') {
            try {
                if($this->model->isUserLoggedIn($this->token))
                {
                    $postData = $this->getFormData();
                    $responseData = $this->model->updateUserInfo($this->model->getUserIdFromToken($this->token), $postData);
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
    public function listDeliveryAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if ($requestMethod === 'POST') {
            try {
                if($this->model->isUserLoggedIn($this->token))
                {
                    $responseData = json_encode($this->model->getUserDeliveryInfos($this->model->getUserIdFromToken($this->token)));
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

    public function uploadDeliveryAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if ($requestMethod === 'POST') {
            try {
                if($this->model->isUserLoggedIn($this->token))
                {
                    $postData = $this->getPostData();
                    if($postData['deliveryID'] == -1)
                    {
                        $responseData = $this->model->addDeliveryInfo($this->model->getUserIdFromToken($this->token), $postData);
                    }
                    else $responseData = $this->model->updateDeliveryInfo($this->model->getUserIdFromToken($this->token), $postData);
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

    public function deleteDeliveryAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if ($requestMethod === 'DELETE') {
            try {
                if($this->model->isUserLoggedIn($this->token))
                {
                    $deliveryID = $this->getSpecificQueryStringParam('delivery-id') ?? -1;
                    if($deliveryID != -1)
                    {
                        $responseData = $this->model->deleteDeliveryInfo($this->model->getUserIdFromToken($this->token), $deliveryID);
                    }
                    else throw new InvalidArgumentException("The delivery information is not exist!");
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
}