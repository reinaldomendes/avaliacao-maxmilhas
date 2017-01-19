<?php

namespace Rbm\Http;

use PHPUnit_Framework_TestCase;
use Rbm\Http\Session;
use Rbm\Http\Session\Flash;

class SessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return Rbm\Http\Request;
     */
    public function createSession($scope)
    {
        $session = new Session($scope);
        $_SESSION = [];

        return $session;
    }

    /**
     * @test
     */
    public function we_should_set_a_property()
    {
        $session = $this->createSession('default');
        $session->name = 'value';
        $this->assertEquals($_SESSION['default']['name'], 'value');
    }
    /**
     * @test
     */
    public function we_should_set_and_get_property()
    {
        $session = $this->createSession('default');
        $session->name = 'value';
        $this->assertEquals($session->name, 'value');
    }

    /**
     * @test
     */
    public function unset_should_unset_a_session_scope()
    {
        $session = $this->createSession('default');
        $session->name = 'value';
        unset($session->name);
        $this->assertFalse(isset($_SESSION['default']['name']));
    }

    /**
     * @test
     */
    public function isset_should_works()
    {
        $session = $this->createSession('default');
        $session->name = 'value';
        $this->assertEquals(isset($session->name), true);
        unset($session->name);
        $this->assertEquals(isset($session->name), false);
    }

    /**
     * @test
     */
    public function clear_should_cleanup_scope()
    {
        $session = $this->createSession('default');
        $session->name = 'value';
        $session->clear();

        $this->assertEquals(isset($session->name), false);
    }

    /**
     * @test
     */
    public function flash_should_return_a_flash_object()
    {
        $session = $this->createSession('default');
        $this->assertInstanceOf(Flash::class, $session->flash());
    }

    /**
     * @test
     */
    public function we_should_be_able_to_set_and_get_value_with_array_syntax()
    {
        $session = $this->createSession('array_access');
        $session['name'] = 'value';
        $this->assertEquals($session['name'], $_SESSION['array_access']['name']);
    }

    /**
     * @test
     */
    public function we_should_be_able_to_isset_with_array_syntax()
    {
        $session = $this->createSession('array_access');
        $session['name'] = 'value';
        $this->assertTrue(isset($session['name']));
    }

    /**
     * @test
     */
    public function we_should_be_able_to_unsset_with_array_syntax()
    {
        $session = $this->createSession('array_access');
        $session['name'] = 'value';
        unset($session['name']);
        $this->assertFalse(isset($session['name']));
    }
    /**
     * @test
     */
    public function we_should_be_able_to_iterate_over_object()
    {
        $session = $this->createSession('array_access');
        $session['name'] = 'value';
        $ok = false;

        foreach ($session as $key => $value) {
            $ok = true;
        }
        $this->assertTrue($ok);
    }
    /**
     * @test
     * @runInSeparateProcess
     */
    public function we_should_be_able_to_call_start_destroy()
    {
        $this->assertEquals(1, Session::start());
        $this->assertEquals(1, Session::destroy());
    }
}
