<?php

namespace Rbm\Di;

/**
 * @todo implement Di logic
 */
class Di
{
    protected $binds = [];

    protected $singletonInstances = [];

    protected $bindParams = [];
    /**
     */
    public function bind($interface, $context)
    {
        $this->binds[$interface] = ['context' => $context,'type' => 'simple'];
    }

    public function bindSingleton($interface, $context)
    {
        $this->binds[$interface] = ['context' => $context,'type' => 'singleton'];
    }
    public function make($interface, array $params = [])
    {
        if (isset($this->binds[$interface])) {
            $context = $this->binds[$interface]['context'];
            if ($this->binds[$interface]['type'] == 'singleton') {
                return $this->singletonInstances[$interface] =
                    isset($this->singletonInstances[$interface]) ?
                     $this->singletonInstances[$interface] : $this->doMake($context, $params);
            }

            return $this->doMake($context, $params);
        }
        throw new \Exception("Bind not found for a {$interface}");
    }

    public function bindParam($name, $value)
    {
        $this->bindParams[$name] = $value;

        return $this;
    }

    public function getParam($name)
    {
        return isset($this->bindParams[$name]) ? $this->bindParams[$name] : null;
    }

    protected function doMake($context, array $params)
    {
        if (is_callable($context)) {
            return call_user_func_array($context, $params);
        } elseif (class_exists($context)) {
            if (!$params) {
                return new $context();
            }

            return new $context($params);
        }
    }
}
