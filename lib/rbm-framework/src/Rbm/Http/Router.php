<?php

namespace Rbm\Http;

class Router
{
    /**
     * @var array - List of routes
     */
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'PUT'  => [],
        'DELETE' => [],
    ];

    /**
     * @var Rbm\Http\Request
     */
    protected $request;

    /**
     * @var Rbm\Http\Response
     */
    protected $response;

    /**
     *
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @var array() - files to include on route process
     */
    protected $routeFiles;

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     *
     */
    public function resource($resource, $controller)
    {
    }

    public function get($path, $controller)
    {
        $this->routes['GET'][$path] = $controller;

        return $this;
    }

    public function setRouteFiles(array $files)
    {
        $this->routeFiles = (array) $files;

        return $this;
    }

    /*
    * @return Rbm\Http\Dispatcher
    */
    public function route()
    {
        $this->includeRouteFiles();
        $route = $this->findRoute();
        if ($route) {
            $dispatcher = new Dispatcher(key($route), current($route));
            $dispatcher->setRequest($this->request)
                    ->setResponse($this->response);
        } else {
            throw new \Exception('Route not found');
        }

        return $dispatcher;
    }

    /**
     * Check if route match.
     */
    public function match($routeString)
    {
        $uri = $this->request->getRequestUri();

        $regexRoute = preg_replace('@:\w[\w\d]*@', '([^/]+)', $routeString);
        $regexRoute = '@^'.$regexRoute.'/?$@';
        $arrayMatches = [];
        $match = preg_match($regexRoute, $uri, $arrayMatches);

        if ($match) {
            $this->setRouteParams($routeString, $arrayMatches);
        }

        return $match;
    }

    /***************************************************************************
    * Protected methods
    ***************************************************************************/

    protected function setRouteParams($routeString, $arrayMatches)
    {
        $routeStringArray = explode('/', $routeString);
        $paramNames = [];
        $i = 0;
        foreach ($routeStringArray as $v) {
            if (strpos($v, ':') !== false) {
                $paramNames[++$i] = substr($v, 1);
            }
        }

        foreach ($arrayMatches as $key => $value) {
            if (isset($paramNames[$key])) {
                $paramName = $paramNames[$key];
                $this->request->setParam($paramName, $value);
            }
        }
    }
    /*
    * @return string|Closure
    */

    protected function findRoute()
    {
        $method = $this->request->getMethod();
        foreach ($this->routes[$method] as $routeString => $controller) {
            if ($this->match($routeString)) {
                return [$routeString => $controller];
            }
        }

        return;
    }

    protected function includeRouteFiles()
    {
        foreach ($this->routeFiles as $file) {
            if (!is_file($file)) {
                throw new \Exception(" Route file '{$file}' not found");
            }
            include $file;
        }
    }
}
