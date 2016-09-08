<?php
/**
 * Xframe is a opensource php framework
 * @author     Hopeness <houpengg@gmail.com>
 * @link       https://github.com/hopeness/Xframe
 * @version    0.2.0 beta
 * @category   Core
 * @package    Core libs
 * @copyright  Copyright (c) 2013-2014 Hopeness (http://www.hopeness.net)
 * @license    https://github.com/hopeness/Xframe/blob/master/LICENSE GNU License
 */

namespace Core\Library;

class Session implements SessionHandlerInterface{

    static private $session = [];

    abstract public bool close ( void )
    abstract public bool destroy ( string $session_id )
    abstract public bool gc ( string $maxlifetime )
    abstract public bool open ( string $save_path , string $name )
    abstract public string read ( string $session_id )
    abstract public bool write ( string $session_id , string $session_data )

    public function __construct(){
        session_start();
    }

    public function __set($key, $val){

    }

    public function __get($key){

    }

    public distory(){
        session_destroy();
    }

}
