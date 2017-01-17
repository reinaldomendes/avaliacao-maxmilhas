<?php

namespace Rbm\Http;

/**
 *
 */
class Request
{
    protected $server = [];
    /**
     * @var array  - GET http params
     */
    protected $get = [];

    /**
     * @var array  - POST http params
     */
    protected $post = [];

    /**
     * @var array  - DELETE http params
     */
    protected $delete = [];

    /**
     * @var array  - PUT http params
     */
    protected $put = [];

    /**
     * @var array - route and user params
     */
    protected $params = [];

    /**
     * @var string - http method
     */
    protected $method = 'GET';

    /**
     * @var bool - method is overried with[_method]
     */
    protected $isMethodOverrided = false;

    /**
     * @var null|string - represent data of a php://input
     */
    protected $rawPhpInput = null;

    public function __construct($rawPhpInput = null)
    {
        $this->setRawPhpInput($rawPhpInput);
        $this->initializeParams();
    }

    /**
     * @return string HTTP method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     *  true when _method field is sent with post data.
     * @return bool
     */
    public function isMethodOverrided()
    {
        return $this->isMethodOverrided;
    }

    /**
     * @param $key string
     * @param $value string
     * @return Rbm\Http\Request
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Return param key if found on (params|post|put|delete|get).
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        $arrayTests = ['params','post','put','delete','get'];
        foreach ($arrayTests as  $property) {
            if (isset($this->{$property}[$key])) {
                return $this->{$property}[$key];
            }
        }

        return $default;
    }

    /**
     * return get params when method is ANY.
     * @return array()
     */
    public function getGetParams()
    {
        return $this->get;

        return [];
    }
    /**
     * return post params when method is POST.
     * @return array()
     */
    public function getPostParams()
    {
        if ($this->getMethod() == 'POST') {
            return $this->post;
        }

        return [];
    }

    /**
     * get a put values from a request.
     * @return array
     **/
    public function getPutParams()
    {
        return $this->put;
    }

    /**
     * get a delete values from a request.
     * @return array
     **/
    public function getDeleteParams()
    {
        return $this->delete;
    }

    /**
     * @return string requestUri
     */
    public function getRequestUri()
    {
        return $this->server['REQUEST_URI'];
    }

    /**
     * @return string php://input
     */
    public function getRawPhpInput()
    {
        if (null === $this->rawPhpInput) {
            $this->rawPhpInput = file_get_contents('php://input');
        }

        return $this->rawPhpInput;
    }
    /**
     * Set alternative string for php://input useful for test.
     * @param string php://input
     * @return Rbm\Http\Request
     */
    public function setRawPhpInput($str)
    {
        $this->rawPhpInput = $str;

        return $this;
    }

    /***************************************************************************
            Protected methods
    /**************************************************************************/

    /**
     * @return Rbm\Http\Request
     */
    protected function initializeParams()
    {
        $this->server = $_SERVER;
        $this->get = $_GET;

        $methodOverrided = (isset($_POST['_method']) ? $_POST['_method'] : false);

        if ($methodOverrided) {
            $this->isMethodOverrided = (bool) $methodOverrided;
            $this->method = strtoupper($methodOverrided);
            $property = strtolower($methodOverrided);
            $this->{$property} = $_POST;
        } else {
            $method = isset($this->server['REQUEST_METHOD']) ? $this->server['REQUEST_METHOD'] : 'GET';
            $this->method = strtoupper($method);
            switch ($method) {
                case 'PUT':
                    parse_str($this->getRawPhpInput(), $this->put);
                    break;
                case 'DELETE':
                    parse_str($this->getRawPhpInput(), $this->delete);
                break;
                default:
                    $this->post = $_POST;
                break;

            }
        }

        return $this;
    }
}
