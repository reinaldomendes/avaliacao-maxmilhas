<?php

namespace Rbm\View;

use PHPUnit_Framework_TestCase;
use Rbm\View\View;
use Rbm\View\Renderer;
use Rbm\Di\Di;

class RendererTest extends PHPUnit_Framework_TestCase
{
    public function createView($name, $vars = [])
    {
        $that = $this;
        $view = new View($name);
        $view->setLocatePaths([__DIR__.'/views'])
        ->setExtensions(['phtml'])
        ->with($vars)
        ->setRenderer(function () use ($view, $that) {
            return $that->create()->render($view);
        });

        return $view;
    }

    public function create()
    {
        $that = $this;

        $renderer = new Renderer(function ($name) use ($that) {
                return $that->createView($name);

        });

        return $renderer;
    }
    /**
     * @test
     */
    public function a_render_should_render_include_script_file()
    {
        $renderer = $this->create();
        $view = $this->createView('index.index', ['variable' => 'value']);
        $result = $renderer->render($view);
        $this->assertEquals('value', trim($result));
    }
    /**
     * @test
     */
    public function a_render_should_works_with_a_partial()
    {
        $renderer = $this->create();
        $result = $renderer->render($this->createView('partials.index'));
        $this->assertEquals('PARTIAL RESULT', trim($result));
    }
    /**
     * @test
     */
    public function a_render_should_works_with_a_extend()
    {
        $renderer = $this->create();
        $result = $renderer->render($this->createView('extends.index'));
        $this->assertEquals('<div>VALUE</div>', preg_replace("@[\s\n\r]+@", '', $result));
    }

    /**
     * @test
     */
    public function a_render_should_works_a_return_inside_ascript()
    {
        $renderer = $this->create();
        $result = $renderer->render($this->createView('return.index'));
        $this->assertEquals('<div>RETURN</div>', preg_replace("@[\s\n\r]+@", '', $result));
    }
}
