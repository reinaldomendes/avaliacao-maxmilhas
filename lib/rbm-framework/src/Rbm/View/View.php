<?php

namespace Rbm\View;

class View
{
    protected $name = null;

    protected $locatePaths = [];

    protected $vars = [];

    protected $renderer = null;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Rbm\View\Renderer | callable
     * @return Rbm\View\View
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;

        return $this;
    }

    /**
     * define view parameters.
     * @param string| array $param name or associative array of params [key => value]
     * @param mixed value - value of a param
     * @return Rbm\View\View
     */
    public function with($param, $value = null)
    {
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $this->with($key, $value);
            }

            return $this;//early return is less Complex tham use else
        }

        $this->vars[$param] = $value;

        return $this;
    }

    /**
     * @return array - variables to a view
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * Magic Method, convert to string.
     * @return string
     */
    public function __toString()
    {
        return (string) $this->render();
    }
    /**
     * @return string
     */
    public function render()
    {
        if (is_callable($this->renderer)) {
            return call_user_func_array($this->renderer, [$this]);
        }

        return $this->renderer->render($this);
    }

    /**
     * Paths to locate a view file.
     * @param array $paths
     * @return Rbm\View\View
     */
    public function setLocatePaths(array $paths)
    {
        $this->locatePaths = $paths;

        return $this;
    }

    /**
     * Set extensions for a view file.
     * ex : php, phtml.
     * @param array $extensions
     * @return Rbm\View\View
     */
    public function setExtensions(array $extensions)
    {
        $this->extensions = $extensions;

        return $this;
    }
    /**
     * Find view script.
     * @return string|null
     */
    public function locateScriptFile()
    {
        $scriptFile = str_replace('.', '/', $this->name);
        foreach ($this->locatePaths as $path) {
            foreach ($this->extensions as $extension) {
                $file = $path.'/'.$scriptFile.'.'.ltrim($extension, '.');
                if (is_file($file)) {
                    return $file;
                }
            }
        }

        if (null === $scriptFile) {
            $paths = implode(',', $view->getLocatePaths);
            throw new \Exception("view script file '{$this->name}' not found in {$paths}");
        }
    }
}
