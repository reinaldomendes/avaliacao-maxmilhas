<?php

namespace App\Models;

use ArrayObject;

class Photo extends ArrayObject
{
    protected $fields = [];
    public function __construct($data)
    {
        parent::__construct($data);
    }
    public function toArray()
    {
        return $this->getArrayCopy();
    }
    public function __get($name)
    {
        if (isset($this[$name])) {
            return $this[$name];
        }

        return;
    }
    public function __set($name, $value)
    {
        $this[$name] = $value;

        return $this;
    }
}
