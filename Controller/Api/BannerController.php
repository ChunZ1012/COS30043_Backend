<?php
require_once PROJECT_ROOT_PATH . "/Model/BannerModel.php";
class BannerController extends BaseController
{
    private $model;
    public function __construct()
    {
        $this->model = new BannerModel();
    }
    public function listAction()
    {
        $strErrorDesc = '';
        $strErrorHeader = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if ($requestMethod === 'GET') {
            try {
                $responseData = json_encode($this->model->getBanners());
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
}

?>