<?php

namespace Rbm\View;

use Rbm\Di\Di;
use Closure;

class Renderer
{
    /**
     * do not use inside vies scripts.
     * @var mixed
     */
    protected $__extends__ = null;

    /**
     * do not use inside vies scripts.
     * @var Closure
     */
    protected $__view_creator__;

    /**
     * set di container.
     * @var callable
     */
    public function __construct(Closure $viewCreatorCallback)
    {
        $this->__view_creator__ = $viewCreatorCallback;
    }

    /**
     * Render view script.
     * @param Rbm\View\View $view
     */
    public function render($view)
    {
        $errorHandler = null;
        /*ignore notice*/
        $errorHandler = set_error_handler(function ($errno) use (&$errorHandler) {
            if ($errno  === E_NOTICE) {
                return;
            }

            return call_user_func_array($errorHandler, func_get_args());
        });

        ob_start();
        extract($view->getVars());

        $result = include $view->locateScriptFile();

        if (is_string($result)) {
            echo $result;
        }
        $result = ob_get_clean();

        if (is_callable($this->__extends__)) {
            $result = call_user_func_array($this->__extends__, [$result]);
        }

        restore_error_handler();

        return $result;
    }

    /* Helper functions to script file.
    * @todo Move helpers to a specific class and implent a __call to magically get helpers
    */
    /**
     * @param string $str
     * @return string
     */
    public function htmlEscape($str)
    {
        return htmlentities($str);
    }

    /**
     * @param string $name - name of a view partial
     * @param array $params - variables to a view partial
     * @return string
     */
    public function partial($name, array $params = [])
    {
        $fn = $this->__view_creator__;

        return  $fn($name)->with($params);
    }

    public function extend($name, array $params = [])
    {
        $fn = $this->__view_creator__;
        $this->__extends__ = function ($result) use ($name, $params, $fn) {

            return $fn($name)->with(array_merge($params, ['content' => $result]));
        };
    }
}
