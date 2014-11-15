<?php

class Exception_PreDispatchException extends Exception implements Exception_iControllerTractableException
{
    private $handler;

    public function __construct($handler, $message = null) {

        parent::__construct($message);

        $this->handler = $handler;
    }

    public function handleIt($param = null) {
        return is_callable($this->handler) ? call_user_func($this->handler, $param) : false;
    }

}
