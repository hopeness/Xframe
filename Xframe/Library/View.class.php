<?php
namespace Xframe\Library;

use Xframe\Api\ViewInterface,
    Xframe\Exception;

/**
 * Base of View
 * @author     Hopeness <houpengg@gmail.com>
 * @category   Core
 * @package    Core base
 */
class View implements ViewInterface
{
    private static $instance = null;

    /**
     * Param array
     * @var array
     */
    private static $parameter = [];

    /**
     * Templet array
     * @var array
     */
    private static $templet = [];

    private function __construct(){

    }

    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Assignment handling
     * @param string $name  Variable name
     * @param mixed  $value Value
     * @return
     **/
    public function assign($name, $value){
        self::$parameter[$name] = $value;
    }

    /**
     * Loading the templates
     * @param string $templet The templet name
     * @return
     **/
    public function display($templet = null){
        if(empty($templet)) $templet = CONTROLLER;
        self::$templet[] = $templet;
    }

    public function view($templet = null){
        if($templet === null) $templet = CONTROLLER;
        if(is_file(SKIN_PATH.$templet.VIEW_EXT)){
             // include SKIN_PATH.$templet.VIEW_EXT;
            //echo file_get_contents(SKIN_PATH.$templet.VIEW_EXT);
            require SKIN_PATH.$templet.VIEW_EXT;
        }else{
            throw new Exception('Templete ERROR:Templete file is non-existent!');
        }
    }
    
    public function __destruct(){
        if(is_array(self::$templet)){
            foreach(self::$templet as $templet){
                if(is_file(SKIN_PATH.$templet.VIEW_EXT)){
                    extract(self::$parameter, EXTR_SKIP);
                    require SKIN_PATH.$templet.VIEW_EXT;
                }else{
                    throw new Exception('Templete ERROR:Templete file is non-existent!');
                }
            }
        }
    }
}
