<?php

require_once CONFIG_PATH . 'database.php';

class Utils_PDOTon {
    
    private static $instance=null;
    
    private function __construct() {
        return null;
    }
    
    public static function getInstance() {
        
        return self::$instance?self::$instance:(self::$instance = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS, [PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]));
        
    }
}

