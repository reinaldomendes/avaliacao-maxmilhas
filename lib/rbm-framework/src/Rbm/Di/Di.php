<?php

namespace Rbm\Di;

use Rbm\Di\Di\Exception as DiException;

class Di
{
    /**
     * @var array - binds to a container
     */
    protected $binds = [];

    /**
     * @var array - Singleton instances instaciated
     */
    protected $singletonInstances = [];

    /**
     * @var array
     */
    protected $bindParams = [];

    /**
     * bind a alias with a context.
     * @param $interface Alias to bind to DiContainer
     * @return Rbm\Di\Di
     */
    public function bind($interface, $context)
    {
        $this->binds[$interface] = ['context' => $context,'type' => 'simple'];

        return $this;
    }

    /**
     * bind a alias with a context for a singleton result.
     * @param $interface Alias to bind to DiContainer
     * @return Rbm\Di\Di
     */
    public function bindSingleton($interface, $context)
    {
        $this->binds[$interface] = ['context' => $context,'type' => 'singleton'];

        return $this;
    }
    /**
     * execute a bind logic.
     * @return mixed
     */
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
        throw new DiException("Bind not found for a {$interface}");
    }

    /**
     * bind parameters.
     * @param string $name
     * @param mixed $vale
     * @return Rbm\Di\Di
     */
    public function bindParam($name, $value)
    {
        $this->bindParams[$name] = $value;

        return $this;
    }
    /**
     * return a value of a binded param.
     * @param string $name
     * @return mixed
     */
    public function getParam($name)
    {
        return isset($this->bindParams[$name]) ? $this->bindParams[$name] : null;
    }

    /*
    * internal logic to create objects
    * @param mixed $context
    * @param array $params
    */
    protected function doMake($context, array $params)
    {
        $result = null;
        if (is_callable($context)) {
            return call_user_func_array($context, $params);
        } elseif (class_exists($context)) {
            switch (count($params)) {
                case 0 :
                    return new $context();
                break;
                case 1:
                    return new $context($params[0]);
                break;
                case 2:
                    return new $context($params[0], $params[1]);
                break;
                case 3:
                    return new $context($params[0], $params[1], $params[2]);
                case 4:
                    return new $context($params[0], $params[1], $params[2], $params[3]);
                break;

                default:
                    $reflection = new \ReflectionClass($context);

                    return $reflection->newInstanceArgs($params);
                break;
            }
        }

        throw new DiException("class not found and context is not callable {$context}");
    }
}
