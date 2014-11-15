<?php

class Exception_MegaCrypterAPIException extends Exception implements Exception_iControllerTractableException
{

    private $handler;

    public function __construct($ecode, $handler = null) {

        parent::__construct(null, $ecode);

        if (is_callable($handler)) {
            $this->handler = $handler;
        } else {
            $this->setDefaultHandler($this->getCode());
        }
    }

    public function handleIt($param = null) {
        return is_callable($this->handler) ? call_user_func($this->handler, $param) : false;
    }

    protected function setDefaultHandler($ecode) {
        
        $this->handler = function(Controller_DefaultController $controller) use ($ecode) {

                    $controller->setViewData(['data' => json_encode(['error' => $ecode])]);
                };
    }

}
