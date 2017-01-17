<?php

namespace Rbm\Http;

use PHPUnit_Framework_TestCase;
use Rbm\Http\Router;
use Rbm\Http\Request;

class RouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return Rbm\Http\Request;
     */
    public function create($uri, $method = 'GET', array $params = [])
    {
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REQUEST_METHOD'] = $method;
        $query = http_build_query($params);

        return  new Router(new Request($query));
    }

    /**
     * @test
     */
    public function we_should_be_able_set_a_get_route()
    {
        $that = $this;
        $fn = function () {

        };
        $router = $this->create('/');
        $router->get('/', $fn);
        $routeResult = $router->route();
        $this->assertEquals(current($routeResult), $fn);
    }
    /**
     * @test
     */
    public function a_get_route_should_not_match_with_a_post()
    {
        $that = $this;
        $fn = function () {

        };
        $router = $this->create('/', 'POST');
        $router->get('/', $fn);
        $routeResult = $router->route();
        $this->assertEquals($routeResult, null);
    }
    /**
     * @test
     */
    public function a_get_route_should_not_match_with_a_put()
    {
        $that = $this;
        $fn = function () {

        };
        $router = $this->create('/', 'PUT');
        $router->get('/', $fn);
        $routeResult = $router->route();
        $this->assertEquals($routeResult, null);
    }
    /**
     * @test
     */
    public function a_get_route_should_not_match_with_a_delete()
    {
        $that = $this;
        $fn = function () {

        };
        $router = $this->create('/', 'DELETE');
        $router->get('/', $fn);
        $routeResult = $router->route();

        $this->assertEquals($routeResult, null);
    }
    /**
     * @test
     */
    public function a_post_route_should_match_with_a_post()
    {
        $that = $this;
        $fn = function () {

        };
        $router = $this->create('/', 'POST');
        $router->post('/', $fn);
        $routeResult = $router->route();

        $this->assertEquals(current($routeResult), $fn);
    }
    /**
     * @test
     */
    public function a_put_route_should_match_with_a_put()
    {
        $that = $this;
        $fn = function () {

        };
        $router = $this->create('/', 'PUT');
        $router->put('/', $fn);
        $routeResult = $router->route();

        $this->assertEquals(current($routeResult), $fn);
    }
    /**
     * @test
     */
    public function a_delete_route_should_match_with_a_delete()
    {
        $that = $this;
        $fn = function () {

        };
        $router = $this->create('/', 'DELETE');
        $router->delete('/', $fn);
        $routeResult = $router->route();

        $this->assertEquals(current($routeResult), $fn);
    }
    /**
     * @test
     */
    public function a_route_with_param_should_set_param_on_request()
    {
        $router = $this->create('/1/', 'DELETE');
        $fn = function () {

        };
        $router->delete('/:id', $fn);
        $router->route();
        $request = $router->getRequest();
        $this->assertEquals($request->getParam('id'), '1');
    }
    /**
     * @test
     */
    public function a_router_should_include_router_files()
    {
        $router = $this->create('/', 'GET');
        $router->setRouteFiles([__DIR__.'/RouterTest/routes.php']);
        $callable = end($router->route());
        $this->assertEquals('ok', $callable());
    }

    /**
     * @test
     */
    public function a_router_should_not_match_with_a_invalid_route()
    {
        $router = $this->create('/not-a-valid-route', 'GET');
        $router->get('/', function () {});
        $result = $router->route();
        $this->assertFalse($result);
    }

    /**
     * @test
     * @expectedException  \Rbm\Http\Router\Exception
     */
    public function a_router_should_throw_exception_when_router_file_not_exists()
    {
        $router = $this->create('/', 'GET');
        $router->setRouteFiles([__DIR__.'/RouterTest/file-not-foud.not-extension']);
        $callable = end($router->route());
        $this->assertEquals('ok', $callable());
    }

    /**
     * @test
     */
    public function a_resource_route_should_register_multiple_routes()
    {
        $that = $this;
        $router = $this->create('/', 'GET');
        $router->resource('assets', 'AssetsController');
        $resultRoutes = $router->getRoutes();
        $expectedResult = [
          'GET' => [
            '/assets' => 'AssetsController@index',
            '/assets/:id/show' => 'AssetsController@show',
            '/assets/create' => 'AssetsController@create',
            '/assets/:id/edit' => 'AssetsController@edit',
          ],
          'POST' => [
            '/assets' => 'AssetsController@store',
          ],
          'PUT' => [
            '/assets/:id' => 'AssetsController@update',
          ],
          'DELETE' => [
            '/assets/:id' => 'AssetsController@destroy',
        ],
    ];
        $equals = true;
        foreach ($resultRoutes as $method => $routes) {
            $testRoutes = $expectedResult[$method];
            $equals = $equals && count(array_diff_assoc($routes, $testRoutes)) === 0;
            $equals = $equals && count(array_diff($routes, $testRoutes)) === 0;
        }
        $this->assertTrue($equals);
    }
}
