<?php

namespace Rbm\View;

class View
{
    protected $name = null;

    protected $locatePaths = [];

    protected $vars = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * define view parameters.
     * @param string| array $param name or associative array of params [key => value]
     * @param mixed value - value of a param
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
        return $this->render();
    }
    /**
     * @return string
     */
    public function render()
    {
        $scriptFile = $this->locateScriptFile();

        if (null === $scriptFile) {
            $paths = implode(',', $this->locatePaths);
            throw new \Exception("view script file '{$this->name}' not found in {$paths}");
        }

        $variables = $this->getVars();

        /*isolate script scope*/
        return call_user_func(function () use ($scriptFile, $variables) {
            ob_start();
            extract($variables);
            include $scriptFile;

            return ob_get_clean();
        });
    }

    /**
     * Paths to locate a view file.
     * @param array $paths
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
     */
    public function setExtensions(array $extensions)
    {
        $this->extensions = $extensions;
    }
    /**
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

        return;
    }
}
