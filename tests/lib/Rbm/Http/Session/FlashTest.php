<?php

namespace Rbm\Http\Session;

use PHPUnit_Framework_TestCase;
use Rbm\Http\Session;
use Rbm\Http\Session\Flash;

class FlashTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @runInSeparateProcess
     */
    public function flash_should_be_a_singleton()
    {
        $_SESSION['__flash__']['messages'] = ['test' => 'test'];
        $flash = Flash::getInstance();
        $this->assertSame($flash, Flash::getInstance());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function clear_should_clear_session_flash()
    {
        $_SESSION['__flash__']['messages'] = ['test' => 'test'];
        $flash = Flash::getInstance();
        $flash->clear();

        $this->assertTrue(empty($_SESSION['__flash__']));
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function flash_should_still_have_messages_after_call_clear()
    {
        $_SESSION['__flash__']['messages'] = ['test' => 'test'];
        $flash = Flash::getInstance();
        $flash->clear();
        $this->assertContains('test', $flash->getMessages());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function flash_should_not_have_messages()
    {
        $_SESSION['__flash__']['messages'] = [];
        $flash = Flash::getInstance();
        $flash->add('ok', 'true');
        $this->assertTrue(empty($flash->getMessages()));
    }
}
