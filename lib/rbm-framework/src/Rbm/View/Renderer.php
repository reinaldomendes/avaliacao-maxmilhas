<?php

namespace Rbm\View;

class Renderer
{
    /**
     * Render view script.
     * @param Rbm\View\View $view
     */
    public function render($view)
    {
        ob_start();
        extract($view->getVars());

        $result = include $view->locateScriptFile();
        if (!is_scalar($result)) {
            echo $result;
        }

        return ob_get_clean();
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
    public function partial($name, array $params)
    {
        return di()->make('View', [$name])->with($params);
    }
}
