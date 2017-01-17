<?php

namespace Rbm\Http;

use Rbm\Http\Controller;
use Rbm\Http\Dispatcher\Exception as DispatcherException;

class Dispatcher
{
    /**
     * @var Rbm\Http\Request
     */
    protected $request;

    /**
     * @var Rbm\Http\Response
     */
    protected $response;

    /**
     * @var string - controller namespace prefix
     */
    protected $controllerNamespace;

    /**
     * @var Rbm\Http\Router
     */
    protected $router;

    /**
     * callable or string to resolve to a controller.
     * @var mixed
     */
    protected $controller;

    /**
     * @var string
     */
    protected $routeString;

    /**
     * @param $callable callable
     */
    public function __construct($routeString, $controller)
    {
        $this->routeString = $routeString;
        $this->controller = $controller;
    }

    /**
     * @return Rbm\Http\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
    /**
     * @return Rbm\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Rbm\Http\Request $request
     * @return Rbm\Http\Dispatcher
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @param Rbm\Http\Response $response
     * @return Rbm\Http\Dispatcher
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @param  string $namespace - controller namespace
     * @return Rbm\Http\Dispatcher
     */
    public function setControllersNamespace($namespace)
    {
        $this->controllerNamespace = $namespace;

        return $this;
    }

    /*
    * @return Rbm\Http\Response
    */
    public function dispatch()
    {
        if (is_callable($this->controller)) {
            $this->dispatchCallable($this->controller);
        } elseif (is_string($this->controller)) {
            $this->dispatchControllerString($this->controller);
        } else {
            $s = print_r($this->controller, true);
            throw new DispatcherException("Controller is not a callable or valid string '{$s}'");
        }

        return $this->response;
    }
    /***************************************************************************
    Protected methods
    ***************************************************************************/
    /**
     * @param string $controller
     */
    protected function dispatchControllerString($controller)
    {
        $arrController = explode('@', $controller);
        if (count($arrController) < 2) {
            throw new DispatcherException("Action not found on a route string '{$this->routeString}'");
        }
        $class = rtrim($this->controllerNamespace, '\\').'\\'.$arrController[0];
        if (!class_exists($class)) {
            throw new DispatcherException("Controller  '{$arrController[0]}' not found at {$this->controllerNamespace}");
        }
        $method = $arrController[1];
        $controllerObject = new $class($this->request, $this->response);
        if (!method_exists($controllerObject, $method)) {
            throw new DispatcherException("Method  '{$method}' not found at controller '{$arrController[0]}'");
        }
        $callable = [$controllerObject,$method];
        $this->dispatchCallable($callable);
    }
    /**
     * @param  callable $callable
     */
    protected function dispatchCallable($callable)
    {
        $result = call_user_func_array($callable, [$this->request, $this->response]);
        if ($result) {
            $this->response->setBody($result);
        }
    }
}
