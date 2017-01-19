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
    /**
     * Singleton accessor.
     * @return Flash
     */
    public static function getInstance()
    {
        return self::$instance = self::$instance ? self::$instance : new static();
    }
    /**
     * Add message to flash.
     * @return Flash
     */
    public function add($type, $message)
    {
        $this->session->messages = array_merge((array) $this->session->messages, [$type => $message]);

        return $this;
    }
    /**
     * return current flash messages;.
     * @return array
     */
    public function getMessages()
    {
        return isset($this->currentMessages) ? $this->currentMessages : [];
    }
    /**
     * clear last session flash.
     */
    public function clear()
    {
        $this->session->clear();
    }
}
