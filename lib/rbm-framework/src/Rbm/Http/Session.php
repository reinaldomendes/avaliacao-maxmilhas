<?php

namespace Rbm\Http;

use Rbm\Http\Session\Flash;
use ArrayAccess;
use IteratorAggregate;
use ArrayIterator;

class Session implements ArrayAccess,IteratorAggregate
{
    protected $scope;

    public function __construct($scope = 'default')
    {
        $this->scope = $scope;
        if (!isset($_SESSION[$this->scope])) {
            $_SESSION[$this->scope] = [];
        }
    }
    public function start()
    {
        session_start();
    }
    public function __get($name)
    {
        $result = isset($_SESSION[$this->scope][$name]) ? $_SESSION[$this->scope][$name] : null;

        return $result;
    }
    public function __set($name, $value)
    {
        $_SESSION[$this->scope][$name] = $value;

        return $this;
    }
    public function __unset($name)
    {
        unset($_SESSION[$this->scope][$name]);
    }
    public function __isset($name)
    {
        return isset($_SESSION[$this->scope][$name]);
    }
    public function clear()
    {
        unset($_SESSION[$this->scope]);
    }
    public function destroy()
    {
        session_destroy();
    }

    public function flash()
    {
        return Flash::getInstance();
    }

    /***************************************************************************
     ArrayAccess interface
    ***************************************************************************/
    public function offsetSet($name, $value)
    {
        return $this->__set($name, $value);
    }
    public function offsetGet($name)
    {
        return $this->__get($name);
    }
    public function offsetExists($name)
    {
        return $this->__isset($name);
    }
    public function offsetUnset($name)
    {
        return $this->__unset($name);
    }
    /***************************************************************************
     IteratorAggregate interface
    ***************************************************************************/
    public function getIterator()
    {
        return new ArrayIterator((array) $_SESSION[$this->scope]);
    }
}
