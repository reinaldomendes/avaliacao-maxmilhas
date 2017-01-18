<?php

namespace Rbm\View;

class Renderer
{
    /*
    * do not use inside vies scripts
    */
    protected $__extends__ = null;
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
        if (!is_scalar($result)) {
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
        return  di()->make('View', [$name])->with($params);
    }

    public function extend($name, array $params = [])
    {
        $this->__extends__ = function ($result) use ($name, $params) {
            return di()->make('View', [$name])->with(array_merge($params, ['content' => $result]));
        };
    }
}
