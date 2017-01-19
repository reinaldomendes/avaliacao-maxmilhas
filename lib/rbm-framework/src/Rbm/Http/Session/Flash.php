<?php

namespace Rbm\Http\Session;

use Rbm\Http\Session;

class Flash
{
    protected static $instance;

    protected $currentMessages;

    protected $session;

    protected $messages;

    protected function __construct()
    {
        $this->session = new Session('__flash__');
        $this->currentMessages = $this->session->messages;//get a copy of a current session
    }
    public static function getInstance()
    {
        return self::$instance = self::$instance ? self::$instance : new static();
    }

    public function add($type, $message)
    {
        $this->session->messages = array_merge((array) $this->session->messages, [$type => $message]);

        return $this;
    }
    public function getMessages()
    {
        return isset($this->currentMessages) ? $this->currentMessages : [];
    }

    public function clear()
    {
        $this->session->clear();
    }
}
