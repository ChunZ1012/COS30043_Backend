<?php
require_once PROJECT_ROOT_PATH . '/inc/Session.php';
class BaseController
{
    /** 
    * __call magic method. 
    */
    public function __call($name, $arguments)
    {
        $this->sendOutput('', array('HTTP/1.1 404 Not Found'));
    }
    /** 
    * Get URI elements. 
    * 
    * @return array 
    */
    protected function getUriSegments()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        return $uri;
    }
    /** 
    * Get querystring params. 
    * 
    * @return array 
    */
    protected function getQueryStringParams()
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        return $query;
    }
    protected function getSpecificQueryStringParam($keyword)
    {
        $params = $this->getQueryStringParams();
        
        if(isset($params[$keyword]) && $params[$keyword]) {
            return $params[$keyword];
        }
        return null;
    }
    protected function getFormData()
    {
        $keys = array_keys($_POST);
        $data = [];

        foreach($keys as $key) $data[$key] = $_POST[$key];
        return $data;
    }
    protected function getPostData()
    {
        try
        {
            return json_decode(file_get_contents('php://input'), true);
        }
        catch(Error $e)
        {
            return '';
        }
    }
    protected function getRequestToken()
    {
        if(isset($_SERVER['HTTP_AUTHORIZATION']))
        {
            if (!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
                return '';
            }
            else return $matches[1];
        }
        else return '';
    }
    protected function handleOutput($data, $errorDesc, $errorHeader)
    {
        if (!$errorDesc) {
            $this->sendOutput(
                $data,
                ['Content-Type: application/json', 'HTTP/1.1 200 OK']
            );
        } else {
            $this->sendOutput(
                json_encode(['error' => $errorDesc]),
                ['Content-Type: application/json', $errorHeader]
            );
        }
    }
    /** 
    * Send API output. 
    * 
    * @param mixed $data 
    * @param string $httpHeader 
    */
    protected function sendOutput($data, $httpHeaders=array())
    {
        header_remove('Set-Cookie');

        header('Access-Control-Allow-Origin: http://localhost:8080');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        
        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }

        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header('HTTP/1.1 200 OK');
        }
        echo $data;
        exit;
    }
}