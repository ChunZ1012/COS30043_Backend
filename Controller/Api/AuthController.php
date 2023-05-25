<?php
require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";
class AuthController extends BaseController 
{
    private $model;
    public function __construct()
    {
        $this->model = new UserModel();
    }
    public function loginAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if ($requestMethod === 'POST') {
            try {
                $postData = $this->getPostData();
                if(isset($postData['email']) && isset($postData['password']))
                {
                    $r = $this->model->checkIfUserEmailExist($postData['email']);

                    if(!isset($r) || $r != 1) throw new InvalidArgumentException('This email has not yet register!');
                    else 
                    {
                        $info = $this->model->getUserInfo($postData['email']);
                        
                        if(!password_verify($postData['password'], $info['user_password'])) throw new InvalidArgumentException('Either email or password is incorrect!');
                        else 
                        {
                            $_SESSION['user_id'] = $info['user_id'];
                            $token = $this->model->getJWTToken($info['user_id'], $info['user_email']);
                            $responseData = json_encode([
                                'error' => false,
                                'token' => $token
                            ]);
                        }
                    }
                }
                else throw new InvalidArgumentException('Please fill in the email and password!');
            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            } catch (Error $e) {
                // $strErrorDesc = 'Something went wrong!';
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        $this->handleOutput($responseData ?? '', $strErrorDesc, $strErrorHeader);
    }

    public function registerAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if ($requestMethod === 'POST') {
            try {
                $postData = $this->getFormData();
                if(isset($postData['email']) && isset($postData['password']))
                {
                    $r = $this->model->checkIfUserEmailExist($postData['email']);

                    if(isset($r) && $r == 1) throw new InvalidArgumentException('This email has been registered! Please login instead');
                    else
                    {
                        $isRegInfoValid = $this->checkRegistrationData($postData);
                        if($isRegInfoValid) 
                        {
                            $r = $this->model->register(
                                $postData['email'],
                                $postData['password'],
                                $postData['displayName'],
                                $postData['contact'],
                                $postData['age'],
                            );

                            if($r < 1) throw new Error();
                            else $responseData = json_encode(['error' => false]);
                        }
                    }
                }
                else throw new InvalidArgumentException('Please fill in the email and password!');
            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            } catch (Error $e) {
                // $strErrorDesc = 'Something went wrong!';
                $strErrorDesc = 'Something went wrong. Please try again later';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        $this->handleOutput($responseData ?? '', $strErrorDesc, $strErrorHeader);
    }
    public function changePasswordAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if ($requestMethod === 'POST') {
            try {
                $postData = $this->getFormData();
                if(isset($postData['email']) && isset($postData['password']))
                {
                    $r = $this->model->checkIfUserEmailExist($postData['email']);

                    if(!isset($r) || $r != 1) throw new InvalidArgumentException('This email has not yet register!');
                    else 
                    {
                        $r = $this->model->changePassword($postData['email'], $postData['password']);
                        
                        if($r < 1) throw new Error();
                        else $responseData = json_encode(['error' => false]);
                    }
                }
                else throw new InvalidArgumentException('Please fill in the email and password!');
            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            } catch (Error $e) {
                // $strErrorDesc = 'Something went wrong!';
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        $this->handleOutput($responseData ?? '', $strErrorDesc, $strErrorHeader);
    }
    public function logoutAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if ($requestMethod === 'POST') {
            try {
                unset($_SESSION['user_id']);
                $_SESSION[] = array();
                session_destroy();

                $responseData = json_encode(['error' => false]);
            } catch(InvalidArgumentException $e) {
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            } catch (Error $e) {
                // $strErrorDesc = 'Something went wrong!';
                $strErrorDesc = $e->getMessage();
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        $this->handleOutput($responseData ?? '', $strErrorDesc, $strErrorHeader);
    }
    protected function checkRegistrationData($data) 
    {
        $required_fields = [
            'email' => "Email",
            'password' => "Password",
            'age' => "Age",
            'displayName' => "Display Name",
            'contact' => "Contact"
        ];

        foreach($required_fields as $k => $val)
        {
            if(!array_key_exists($k, $data)) throw new InvalidArgumentException($val.' is missing!');
        }

        return true;
    }
}