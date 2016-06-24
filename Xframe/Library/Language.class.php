<?php

namespace Xframe\Library;

class Language
{
    private static $instance = null;

    private function __construct(){

    }

    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new self;
        }
        return self::$instance;
    }
}
