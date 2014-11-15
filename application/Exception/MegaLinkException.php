<?php

class Exception_MegaLinkException extends Exception_LinkException
{

    const PREFIX = 'MEGA file';

    public function errorCode2Message($ecode) {
        $emessages = array
            (
            Utils_MegaApi::EINTERNAL => 'MEGA cloud temporarily unavailable',
            Utils_MegaApi::ETEMPUNAVAIL => '%p temporarily unavailable',
            Utils_MegaApi::ENOENT => '%p not found!',
            Utils_MegaApi::EBLOCKED => '%p is blocked!',
            Utils_MegaApi::EKEY => 'Bad %p!',
            Utils_MegaApi::ETOOMANY => "%p not found\n(user account was terminated)"
        );

        return array_key_exists($ecode, $emessages) ? strtr($emessages[$ecode], ['%p' => self::PREFIX]) : self::PREFIX . ' error (' . $ecode . ')';
    }

    protected function setDefaultHandler($ecode=null) {
        $message = $this->getMessage();

        if (in_array($ecode, [Utils_MegaApi::ENOENT, Utils_MegaApi::EBLOCKED, Utils_MegaApi::EKEY, Utils_MegaApi::ETOOMANY])) {

            $this->handler = function(Controller_DefaultController $controller) use ($message) {

                        header("HTTP/1.0 404 Not Found");
                        $controller->setViewData(['message' => $message]);
                        $controller->setTemplateFile('linkerror.html');
                    };
        } else {

            parent::setDefaultHandler();
        }
    }

}


