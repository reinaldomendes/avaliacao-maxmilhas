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
   * @var array() - files to include on route process
   */
  protected $routeFiles;

    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     *
     */
    public function resource($resource, $controller)
    {
        $this->get('/'.$resource, $controller.'@index'); #list
        $this->get('/'.$resource.'/:id/show', $controller.'@show'); #show
        $this->get('/'.$resource.'/create', $controller.'@create'); #create
        $this->get('/'.$resource.'/:id/edit', $controller.'@edit'); #edit
        $this->post('/'.$resource, $controller.'@store'); #store
        $this->put('/'.$resource.'/:id', $controller.'@update'); #list
        $this->delete('/'.$resource.'/:id', $controller.'@destroy'); #list
    }

    /**
     * Register a GET route.
     * @param string $path
     * @param mixed $controller
     * @return Rbm\Http\Router
     */
    public function get($path, $controller)
    {
        $this->routes['GET'][$path] = $controller;

        return $this;
    }

    /**
     * Register a POST route.
     * @param string $path
     * @param mixed $controller
     * @return Rbm\Http\Router
     */
    public function post($path, $controller)
    {
        $this->routes['POST'][$path] = $controller;

        return $this;
    }

    /**
     * Register a PUT route.
     * @param string $path
     * @param mixed $controller
     * @return Rbm\Http\Router
     */
    public function put($path, $controller)
    {
        $this->routes['PUT'][$path] = $controller;

        return $this;
    }

    /**
     * Register a DELETE route.
     * @param string $path
     * @param mixed $controller
     * @return Rbm\Http\Router
     */
    public function delete($path, $controller)
    {
        $this->routes['DELETE'][$path] = $controller;

        return $this;
    }

    /**
     *  Register an array of files to include.
     * @param array $files
     */
    public function setRouteFiles(array $files)
    {
        $this->routeFiles = (array) $files;

        return $this;
    }

    /*
    * @return array found route
    */
    public function route()
    {
        $this->includeRouteFiles();

        return  $this->findRoute();
    }

    /*
    * @return string|Closure
    */

    protected function findRoute()
    {
        $method = $this->request->getMethod();
        $matches = [];
        foreach ($this->routes[$method] as $routeString => $controller) {
            if ($priority = $this->match($routeString)) {
                if (!isset($matches[$priority])) {
                    $matches[$priority] = [$routeString => $controller];
                }
            }
        }
        ksort($matches);

        return current($matches);
    }
    /**
     * Check if route match.
     * @return bool|int - false if not matched int priority of a match route
     */
    public function match($routeString)
    {
        $uri = $this->request->getRequestPath();
        $regexRouteParam = '@:\w[\w\d]*@';
        $priority = null;
        if (preg_match($regexRouteParam, $routeString)) {
            $regexRoute = preg_replace($regexRouteParam, '([^/]+)', $routeString);
            $regexRoute = '@^'.$regexRoute.'/?$@';
            $priority = 2;
        } else {
            $regexRoute = '@^'.$routeString.'/?$@';
            $priority = 1;
        }
        $arrayMatches = [];
        $match = preg_match($regexRoute, $uri, $arrayMatches);
        if ($match) {
            $this->setRouteParams($routeString, $arrayMatches);

            return $priority;
        }

        return false;
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
