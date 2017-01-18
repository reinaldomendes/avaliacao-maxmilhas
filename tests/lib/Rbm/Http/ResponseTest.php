<?php

namespace Rbm\Http;

use PHPUnit_Framework_TestCase;
use Rbm\Http\Response;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Rbm\Http\Response;
     */
    protected $response;
    /**
     * @return Rbm\Http\Request;
     */
    public function setup()
    {
        $this->response = new Response;
    }

    /**
     * @test
     */
    public function we_should_be_able_to_set_body()
    {
        $this->response->setBody('value');
        $this->assertEquals('value', $this->response->getBody());
    }
    /**
     * @test
     */
    public function we_should_be_able_to_stringfy_a_response()
    {
        $this->response->setBody('value');
        $this->assertEquals('value', (string) $this->response);
    }
    /**
     * @test
     */
    public function we_should_be_abe_to_set_headers()
    {
        $location = 'http://xxxx.xxx';
        $this->response->setHeader('location', $location);
        $this->assertEquals($this->response->getHeader('LOCATION'), $location);
    }
    /**
     * @test
     */
    public function we_should_be_able_to_set_and_send_http_response_code()
    {
        $status = 501;
        $this->response->setHttpResponseCode($status)
                        ->sendHttpStatusCode();
        $this->assertEquals(http_response_code(), $status);
    }

    /**
     *  @test
     *  @runInSeparateProcess
     */
    public function we_should_be_able_to_redirect()
    {
        $this->response->setRedirect('/uri', 301);
        $this->response->sendHeaders();
        $arrayHeaders = [];
        foreach (xdebug_get_headers() as $header) {
            $arrayHeader = explode(':', $header);
            $arrayHeaders[$arrayHeader[0]] = $arrayHeader[1];
        }

        $this->assertEquals($arrayHeaders['location'], '/uri');
        $this->assertEquals(301, http_response_code());
    }
    /**
     *  @test
     *  @runInSeparateProcess
     */
    public function we_should_be_able_to_send_headers()
    {
        $this->response->setHeader('x-cache', 'varnish');
        $this->response->sendHeaders();
        $arrayHeaders = [];
        foreach (xdebug_get_headers() as $header) {
            $arrayHeader = explode(':', $header);
            $arrayHeaders[$arrayHeader[0]] = $arrayHeader[1];
        }

        $this->assertEquals($arrayHeaders['x-cache'], 'varnish');
    }
    /**
     *  @test
     *  @runInSeparateProcess
     */
    public function we_shoud_be_able_to_send_data()
    {
        ob_start();
        $this->response
            ->setBody('value')
            ->setHeader('x-cache', 'varnish')
            ->setHttpResponseCode(201)
            ->send();

        $arrayHeaders = [];
        foreach (xdebug_get_headers() as $header) {
            $arrayHeader = explode(':', $header);
            $arrayHeaders[$arrayHeader[0]] = $arrayHeader[1];
        }

        $this->assertEquals(http_response_code(), 201);
        $this->assertEquals($arrayHeaders['x-cache'], 'varnish');
        $this->assertEquals('value', ob_get_clean());
    }
}
