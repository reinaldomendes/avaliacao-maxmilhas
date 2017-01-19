<?php

namespace Rbm\View;

use PHPUnit_Framework_TestCase;
use Rbm\View\View;
use Rbm\View\Renderer;

class ViewTest extends PHPUnit_Framework_TestCase
{
    public function create($name)
    {
        $view = new View($name);

        return $view;
    }

    /**
     * @test
     */
    public function a_view_should_have_a_name()
    {
        $view = $this->create('index.index');
        $this->assertEquals('index.index', $view->getName());
    }

    /**
     * @test
     */
    public function a_view_should_accepts_an_array_on_with_method_and_get_vars()
    {
        $view = $this->create('index.index');
        $view->with(['variable' => 'value']);
        $this->assertEquals($view->getVars(), ['variable' => 'value']);
    }
    /**
     * @test
     */
    public function a_view_should_accepts_2_params_on_with_method_and_get_vars()
    {
        $view = $this->create('index.index');
        $view->with('variable', 'value');
        $this->assertEquals($view->getVars(), ['variable' => 'value']);
    }
    /**
     * @test
     */
    public function a_view_should_call_a_closure_as_view_renderer()
    {
        $view = $this->create('index.index');

        /*Mock a a renderer*/
        $shouldBeCalled = $this->getMock(Renderer::class, ['render'], [function ($name) {}]);
        $shouldBeCalled->expects($this->once())
        ->method('render')
        ->willReturn('rendered');

        $view->setRenderer($shouldBeCalled);
        $this->assertEquals((string) $view, 'rendered');
    }

    /**
     * @test
     */
    public function a_view_should_render_with_class_with_render_method()
    {
        $view = $this->create('index.index');

        /*Mock a closure*/
        $shouldBeCalled = $this->getMock(\stdClass::class, ['__invoke']);
        $shouldBeCalled->expects($this->once())
        ->method('__invoke')
        ->willReturn('ok');

        $view->setRenderer($shouldBeCalled);
        $this->assertEquals((string) $view, 'ok');
    }

    /**
     * @test
     * @expectedException Rbm\View\View\Exception
     */
    public function a_view_should_throw_exception_when_not_locate_script_file()
    {
        $view = $this->create('index.index');
        $view->setLocatePaths([__DIR__.'/views']);
        $view->setExtensions(['no']);
        $view->locateScriptFile();
    }

    /**
     * @test
     */
    public function a_view_should_locate_script_with_dot_syntax()
    {
        $view = $this->create('index.index');
        $view->setLocatePaths([__DIR__.'/views']);
        $view->setExtensions(['phtml']);
        $this->assertTrue(strpos($view->locateScriptFile(), 'views/index/index.phtml') !== false);
    }
}
