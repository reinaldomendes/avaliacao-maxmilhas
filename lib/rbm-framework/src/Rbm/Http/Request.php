<?php

namespace Rbm\Http;

/**
 *
 */
class Request
{
    protected $server = [];
    protected $get = [];
    protected $post = [];
    protected $delete = [];
    protected $put = [];
    protected $params = [];

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->get = $_GET;
        $this->post = $_POST;
    }

    /**
     * @return string HTTP method
     */
    public function getMethod()
    {
        $default = isset($this->server['REQUEST_METHOD']) ? $this->server['REQUEST_METHOD'] : null;
        $method = $this->getParam('_method', $default);

        return strtoupper($method);
    }

    /**
     *  true when _method field is sent with post data.
     * @return bool
     */
    public function isMethodOverrided()
    {
        return (bool) (isset($this->post['_method']) ? $this->post['_method'] : false);
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
        if ($this->getMethod() == 'PUT') {
            if ($this->isMethodOverrided()) {
                return $this->post;
            } else {
                parse_str($this->getPhpInput(), $postVars);

                return $postVars;
            }
        }

        return [];
    }

    /**
     * get a delete values from a request.
     * @return array
     **/
    public function getDeleteParams()
    {
        if ($this->getMethod() == 'DELETE') {
            if ($this->isMethodOverrided()) {
                return $this->post;
            } else {
                parse_str($this->getPhpInput(), $postVars);

                return $postVars;
            }
        }

        return [];
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

    public function getRequestUri()
    {
        return $this->server['REQUEST_URI'];
    }

    /***************************************************************************
            Protected methods
    /**************************************************************************/

    protected function getPhpInput()
    {
        return file_get_contents('php://input');
    }
}
