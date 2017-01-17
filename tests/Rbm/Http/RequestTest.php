<?php

namespace Rbm\Http;

use PHPUnit_Framework_TestCase;
use Rbm\Http\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
    protected static $server;

    public static function setUpBeforeClass()
    {
        self::$server = $_SERVER;
    }
    public static function tearDownAfterClass()
    {
        $_SERVER = self::$server;
        $_REQUEST = $_POST = $_GET = [];
    }

    public function setup()
    {
        $_SERVER = self::$server;
        $_REQUEST = $_POST = $_GET = [];
    }

    /**
     * @return Rbm\Http\Request;
     * @covers  initializeParams
     */
    public function createRequest($phpInput = null)
    {
        return new Request($phpInput);
    }

    /**
     * @test
     */
    public function we_should_be_able_to_get_method()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $this->assertEquals($this->createRequest()->getMethod(), $_SERVER['REQUEST_METHOD']);
    }

    /**
     * @test
     */
    public function we_should_get_true_on_is_method_overrided()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $overrideMethod = 'DELETE';
        $_POST['_method'] = $overrideMethod;
        $request = $this->createRequest();

        $this->assertTrue($request->isMethodOverrided());
    }

    /**
     * @test
     */
    public function we_should_get_false_on_is_method_overrided()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $request = $this->createRequest();

        $this->assertFalse($request->isMethodOverrided());
    }

    /**
     * @test
     */
    public function we_should_get_method_with_method_overrided()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $overrideMethod = 'DELETE';
        $_POST['_method'] = $overrideMethod;
        $request = $this->createRequest();

        $this->assertEquals($request->getMethod(), $overrideMethod);
    }

    /**
     * @test
     */
    public function we_should_get_a_default_param()
    {
        $request = $this->createRequest();
        $this->assertEquals($request->getParam('test', 'default'), 'default');
    }

    /**
     * @test
     */
    public function we_should_set_a_param_with_set_param_method()
    {
        $request = $this->createRequest();
        $request->setParam('test', 'value');
        $this->assertEquals($request->getParam('test', 'default'), 'value');
    }

    /**
     * @test
     */
    public function set_param_should_have_priority_over_others()
    {
        $_GET['test'] = $_POST['test'] = 'OTHERTHING';
        $request = $this->createRequest();
        $request->setParam('test', 'value');
        $this->assertEquals($request->getParam('test', 'default'), 'value');
    }

    /**
     * @test
     */
    public function we_should_get_a_param_with_get_param_method()
    {
        $_GET['test'] = 'value';
        $request = $this->createRequest();
        $this->assertEquals($request->getParam('test', 'default'), $_GET['test']);
    }
     /**
      * @test
      */
     public function we_should_get_a_post_with_get_param_method()
     {
         $_POST['test'] = 'value';
         $request = $this->createRequest();
         $this->assertEquals($request->getParam('test'), $_POST['test']);
     }

     /**
      * We can get getParams when method is POST or any others.
      * @test
      */
     public function we_should_get_values_with_get_get_params_method_when_method_is_get()
     {
         $_GET['test'] = 'value';
         $this->server['REQUEST_METHOD'] = 'GET';
         $request = $this->createRequest();
         $this->assertEquals($request->getGetParams(), $_GET);
     }
     /**
      * We can get getParams when method is POST.
      * @test
      */
     public function we_should_get_values_with_get_get_params_method_when_method_is_post()
     {
         $_GET['test'] = 'value';
         $this->server['REQUEST_METHOD'] = 'POST';
         $request = $this->createRequest();
         $this->assertEquals($request->getGetParams(), $_GET);
     }
     /**
      * We can get getParams when method is POST or any others.
      * @test
      */
     public function we_should_get_values_with_get_get_params_method_when_method_is_put()
     {
         $_GET['test'] = 'value';
         $this->server['REQUEST_METHOD'] = 'PUT';
         $request = $this->createRequest();
         $this->assertEquals($request->getGetParams(), $_GET);
     }
     /**
      * We can get getParams when method is POST or any others.
      * @test
      */
     public function we_should_get_values_with_get_get_params_method_when_method_is_delete()
     {
         $_GET['test'] = 'value';
         $this->server['REQUEST_METHOD'] = 'DELETE';
         $request = $this->createRequest();
         $this->assertEquals($request->getGetParams(), $_GET);
     }

     /**
      * @test
      */
     public function we_should_get_a_post_with_get_post_params_method_when_method_is_post()
     {
         $_POST['test'] = 'value';
         $_POST['_method'] = 'POST';
         $request = $this->createRequest();
         $this->assertEquals($request->getPostParams(), $_POST);
     }
    /**
     * @test
     */
    public function we_should_not_get_a_post_with_get_post_params_method_when_method_is_not_post()
    {
        $_POST['test'] = 'value';
        $_POST['_method'] = 'PUT';
        $request = $this->createRequest();
        $this->assertEquals($request->getPostParams(), []);
    }

    /**
     * @test
     */
    public function we_should_get_a_put_with_get_put_params_method_when_method_is_put_with_method_override()
    {
        $_POST['test'] = 'value';
        $_POST['_method'] = 'PUT';
        $request = $this->createRequest();
        $this->assertEquals($request->getPutParams(), $_POST);
    }
    /**
     * @test
     */
    public function we_should_get_a_put_with_get_put_params_method_when_method_is_put()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $strValue = 'test=value&test2=value2';
        parse_str($strValue, $testValue);
        $request = $this->createRequest($strValue);

        $this->assertEquals($request->getPutParams(), $testValue);
    }

    /**
     * @test
     */
    public function we_should_get_a_delete_with_get_delete_params_method_when_method_is_delete()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $strValue = 'test=value&test2=value2';
        parse_str($strValue, $testValue);

        $request = $this->createRequest($strValue);

        $this->assertEquals($request->getDeleteParams(), $testValue);
    }

    /**
     * @test
     */
    public function we_should_get_a_delete_with_get_delete_params_method_when_method_is_delete_with_method_override()
    {
        $_POST['test'] = 'value';
        $_POST['_method'] = 'DELETE';
        $request = $this->createRequest();
        $this->assertEquals($request->getDeleteParams(), $_POST);
    }

    /**
     * @test
     */
    public function we_should_get_a_request_uri()
    {
        $_SERVER['REQUEST_URI'] = '/request';
        $request = $this->createRequest();
        $this->assertEquals($request->getRequestUri(), $_SERVER['REQUEST_URI']);
    }
}
