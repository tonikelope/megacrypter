<?php

class Exception_MegaCrypterLinkException extends Exception_LinkException
{
    const PREFIX = 'MC link';

    public function errorCode2Message($ecode) {
        $emessages = array
            (
            Utils_MegaCrypter::LINK_ERROR => 'Bad %p!',
            Utils_MegaCrypter::BLACKLISTED_LINK => 'Blocked %p!',
            Utils_MegaCrypter::EXPIRED_LINK => 'Expired %p!'
        );

        return array_key_exists($ecode, $emessages) ? strtr($emessages[$ecode], ['%p' => self::PREFIX]) : self::PREFIX . ' error (' . $ecode . ')';
    }

    protected function setDefaultHandler($ecode=null) {
        $message = $this->getMessage();

        if (in_array($ecode, [Utils_MegaCrypter::BLACKLISTED_LINK, Utils_MegaCrypter::LINK_ERROR])) {
            $this->handler = function(Controller_DefaultController $controller) {

                        header("HTTP/1.0 404 Not Found");
                        $controller->setViewData(['error' => 404]);
                        $controller->setTemplateFile('httperror.html');
                    };
        } else if (in_array($ecode, [Utils_MegaCrypter::EXPIRED_LINK])) {

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


