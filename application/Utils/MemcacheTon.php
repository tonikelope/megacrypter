<?php

require_once CONFIG_PATH . 'memcache.php';

class Utils_MemcacheTon {

    private static $instance=null;
    
    private function __construct() {
        return null;
    }
    
    public static function getInstance() {
        
        if (self::$instance) {
            
            $instance = self::$instance;
            
        } else {
            $instance = (self::$instance = new Memcache());
            $instance->connect(MEMCACHE_HOST, MEMCACHE_PORT);
        }
        
        return $instance;
    }
}

