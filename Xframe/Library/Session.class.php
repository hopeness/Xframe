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

namespace Xframe\Library;

/**
 * Session封装类
 * 主要抽象session为框架中的对象操作
 * 同时可以自动添加键前缀
 */
class Session {
    static private $instance = null;
    static private $prefix = '';

    static public function getInstance(){
        if(self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 类初始化
     * @param string $prefix session前缀
     */
    private function __construct(){
        session_start();
        self::$prefix = C('SESSION.PREFIX', '');
    }

    /**
     * 变量设置魔术方法
     * @param string $key 变量名
     * @param mixed  $val 变量值
     */
    public function __set($key, $val){
        $_SESSION[self::prefix($key)] = $val;
    }

    /**
     * 获取变量魔术方法
     * @param  string $key 变量名
     * @return mixed       返回值
     */
    public function __get($key){
        if(isset($_SESSION[self::prefix($key)])){
            return $_SESSION[self::prefix($key)];
        }else{
            return null;
        }
    }

    /**
     * 是否设置变量魔术方法
     * @param  string  $key 变量名
     * @return boolean      返回变量是否被设置
     */
    public function __isset($key){
        return isset($_SESSION[self::prefix($key)]);
    }

    /**
     * 变量注销魔术方法
     * @param string $key 变量名
     */
    public function __unset($key){
        unset($_SESSION[self::prefix($key)]);
    }

    /**
     * 设置变量前缀
     * @param  string $key 变量名
     * @return string      返回附带前缀的变量名
     */
    static private function prefix($key){
        return self::$prefix.$key;
    }

    /**
     * 销毁session
     */
    public function destory(){
        session_unset();
        session_destroy();
    }

    /**
     * 返回所有session数据
     * @return mixed 返回session数据
     */
    public function all(){
        return $_SESSION;
    }

}
