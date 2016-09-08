<?php

namespace Xframe\Library;

class Config
{
    private static $_CONFIG = [];
    private static $_DEFAULT = null;
    private static $instance = null;

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct(){
        
    }

    public function __get($key){
        if(isset(self::$_CONFIG[$key])){
            return self::$_CONFIG[$key];
        }else{
            return self::$_DEFAULT;
        }
    }

    public function __set($key, $value){
        
    }

    public static function setDefault($value = null){
        static::$_DEFAULT = $value;
        return self::$instance;
    }
    
    public function setConfig($value = null){
        if($value){
            self::$_CONFIG = array_merge(self::$_CONFIG, $value);
        }
    }

}

