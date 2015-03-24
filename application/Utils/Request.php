<?php

class Utils_Request
{
    private $_extra;

    public function __construct(array $extra = []) {
        
        $this->_extra = $extra;
    }
    
    public function getVar($id = null) {
        return is_null($id) ? $_GET : (array_key_exists($id, $_GET) ? $_GET[$id] : null);
    }

    public function getPostVar($id = null) {
        return is_null($id) ? $_POST : (array_key_exists($id, $_POST) ? $_POST[$id] : null);
    }

    public function getCookieVar($id = null) {
        return is_null($id)? : $_COOKIE(array_key_exists($id, $_COOKIE) ? $_COOKIE[$id] : null);
    }

    public function getServerVar($id = null) {
        return is_null($id) ? $_SERVER : (array_key_exists($id, $_SERVER) ? $_SERVER[$id] : null);
    }

    public function getSessionVar($id = null) {
        return is_null($id) ? $_SESSION : (array_key_exists($id, $_SESSION) ? $_SESSION[$id] : null);
    }

    public function getFileVar($id = null) {
        return is_null($id) ? $_FILES : (array_key_exists($id, $_FILES) ? $_FILES[$id] : null);
    }

    public function getEnvVar($id = null) {
        return is_null($id) ? $_ENV : (array_key_exists($id, $_ENV) ? $_ENV[$id] : null);
    }

    public function getExtraVar($id = null) {
        return is_null($id) ? $this->_extra : (array_key_exists($id, $this->_extra) ? $this->_extra[$id] : null);
    }
}
