<?php

namespace Rbm\Http;

use PHPUnit_Framework_TestCase;
use Rbm\Http\Request;
use Rbm\Http\Response;
use Rbm\Http\Dispatcher;

require __DIR__.'/DispatcherTest/Controllers/TestController.php';

class DispatcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return Rbm\Http\Dispatcher;
     */
    public function create($routeString, $controller)
    {
        $dispatcher = new Dispatcher($routeString, $controller);
        $dispatcher->setRequest(new Request([]))
                    ->setResponse(new Response())
                    ->setControllersNamespace('\\Rbm\\Http\\DispatcherTest\\Controllers');

        return $dispatcher;
    }
    /**
     * @test
     */
    public function a_dispatcher_should_have_a_request()
    {
        $fn = function () {
            return 'ok';
        };
        $dispatcher = $this->create('/', $fn);

        $this->assertInstanceOf(Request::class, $dispatcher->getRequest());
    }

    /**
     * @test
     */
    public function a_dispatcher_should_have_a_response()
    {
        $fn = function () {
            return 'ok';
        };
        $dispatcher = $this->create('/', $fn);

        $this->assertInstanceOf(Response::class, $dispatcher->getResponse());
    }

    /**
     * @test
     */
    public function a_dispatcher_should_resoulve_a_callable()
    {
        $fn = function () {
            return 'ok';
        };
        $dispatcher = $this->create('/', $fn);

        $result = $dispatcher->dispatch();
        $this->assertEquals((string) $result, $fn());
    }
    /**
     * @test
     */
    public function a_dispatcher_should_resolve_a_controller_string_to_callable()
    {
        $dispatcher = $this->create('/', 'TestController@index');
        $result = $dispatcher->dispatch();
        $this->assertEquals('Rbm\Http\DispatcherTest\Controllers\TestController::index', (string) $result);
    }

    /**
     * @test
     * @expectedException Rbm\Http\Dispatcher\Exception
     */
    public function a_dispatcher_should_throw_exception_when_a_controller_not_found()
    {
        $dispatcher = $this->create('/', 'NotFound@index');
        $result = $dispatcher->dispatch();
    }

    /**
     * @test
     * @expectedException Rbm\Http\Dispatcher\Exception
     */
    public function a_dispatcher_should_throw_exception_a_route_is_invalid()
    {
        $dispatcher = $this->create('/', 'NotFound');
        $result = $dispatcher->dispatch();
    }

    /**
     * @test
     * @expectedException Rbm\Http\Dispatcher\Exception
     */
    public function a_dispatcher_should_throw_exception_a_method_not_found_on_controller()
    {
        $dispatcher = $this->create('/', 'TestController@_method_not_exists');
        $result = $dispatcher->dispatch();
    }

    /**
     * @test
     * @expectedException Rbm\Http\Dispatcher\Exception
     */
    public function a_dispatcher_should_throw_when_a_controller_is_a_invalid_type()
    {
        $dispatcher = $this->create('/', new \ArrayObject([]));
        $result = $dispatcher->dispatch();
    }
}
