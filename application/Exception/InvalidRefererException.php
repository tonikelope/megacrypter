<?php

class Exception_InvalidRefererException extends Exception implements Exception_iControllerTractableException
{
    private $handler;

    public function __construct($handler = null, $message = null) {

        parent::__construct($message);

        if (is_callable($handler)) {
            $this->handler = $handler;
        } else {
            $this->setDefaultHandler();
        }
    }

    public function handleIt($param = null) {
        return is_callable($this->handler) ? call_user_func($this->handler, $param) : false;
    }

    protected function setDefaultHandler() {
        
        $message = $this->getMessage();

        $this->handler = function(Controller_DefaultController $controller) use ($message) {
            
            header('HTTP/1.0 403 Forbidden');
            $controller->setViewData(['message' => $message]);
            $controller->setTemplateFile('linkwarningnedry.html');
        };
    }

}
