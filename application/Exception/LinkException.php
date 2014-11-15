<?php

abstract class Exception_LinkException extends Exception implements Exception_iControllerTractableException
{

    protected $handler;

    abstract protected function errorCode2Message($ecode);

    public function __construct($ecode, $handler = null) {

        parent::__construct($this->errorCode2Message($ecode), $ecode);

        if (is_callable($handler)) {
            $this->handler = $handler;
        } else {
            $this->setDefaultHandler($ecode);
        }
    }

    public function handleIt($param = null) {
        return is_callable($this->handler) ? call_user_func($this->handler, $param) : false;
    }

    protected function setDefaultHandler($ecode=null) {
        $message = $this->getMessage();

        $this->handler = function(Controller_DefaultController $controller) use ($message) {

                    $controller->setViewData(['message' => $message]);
                    $controller->setTemplateFile('linkerror.html');
                };
    }

}

