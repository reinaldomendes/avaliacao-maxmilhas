<?php

namespace Rbm\Http;

use Rbm\Http\Session\Flash;
use ArrayAccess;
use IteratorAggregate;
use ArrayIterator;

class Session implements ArrayAccess,IteratorAggregate
{
    protected $scope;

    /**
     * Initialize a session with a scope.
     */
    public function __construct($scope = 'default')
    {
        $this->scope = $scope;
        if (!isset($_SESSION[$this->scope])) {
            $_SESSION[$this->scope] = [];
        }
    }
    /**
     * start a php_session.
     */
    public static function start()
    {
        return session_start();
    }

    /**
     * destroy a php session.
     */
    public static function destroy()
    {
        return session_destroy();
    }

    /**
     * magic getter.
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $result = isset($_SESSION[$this->scope][$name]) ? $_SESSION[$this->scope][$name] : null;

        return $result;
    }

    /**
     * magic setter.
     * @param string $name
     * @param mixed $value
     * @return Rbm\Http\Session
     */
    public function __set($name, $value)
    {
        $_SESSION[$this->scope][$name] = $value;

        return $this;
    }

    /**
     * magic unset.
     * @param string $name
     */
    public function __unset($name)
    {
        unset($_SESSION[$this->scope][$name]);
    }

    /**
     * magic isset.
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($_SESSION[$this->scope][$name]);
    }
    /**
     * Clear all variables on this session scope.
     * @return Rbm\Http\Session
     */
    public function clear()
    {
        unset($_SESSION[$this->scope]);

        return $this;
    }

    /**
     * Return a instance of a sessionFlash.
     * @return Flash
     */
    public function flash()
    {
        return Flash::getInstance();
    }

    /***************************************************************************
     ArrayAccess interface
    ***************************************************************************/
    /**
     * @inheritdoc.
     */
    public function offsetSet($name, $value)
    {
        return $this->__set($name, $value);
    }
    /**
     * @inheritdoc.
     */
    public function offsetGet($name)
    {
        return $this->__get($name);
    }
    /**
     * @inheritdoc.
     */
    public function offsetExists($name)
    {
        return $this->__isset($name);
    }
    /**
     * @inheritdoc.
     */
    public function offsetUnset($name)
    {
        return $this->__unset($name);
    }
    /***************************************************************************
     IteratorAggregate interface
    ***************************************************************************/
    /**
     * @inheritdoc.
     */
    public function getIterator()
    {
        return new ArrayIterator((array) $_SESSION[$this->scope]);
    }
}
