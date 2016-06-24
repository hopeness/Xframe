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

class Cookie
{
    static private $instance = null;
    static private $prefix;
    static private $expire;
    static private $path;
    static private $domain;
    static private $secure;
    static private $httponly;

    static public function getInstance(){
        if(self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 类初始化
     * @param string $prefix cookie前缀
     */
    private function __construct(){
        self::$prefix = C('COOKIE.PREFIX', '');
        self::$expire = C('COOKIE.EXPIRE', 1800);
        self::$path = C('COOKIE.PATH', '/');
        self::$domain = C('COOKIE.DOMAIN', C('DOMAIN'));
        self::$secure = C('COOKIE.SECURE', false);
        self::$httponly = C('COOKIE.HTTPONLY', false);
    }

    /**
     * 变量设置魔术方法
     * @param string $key 变量名
     * @param mixed  $val 变量值
     * @return bool       返回设置结果
     */
    public function __set($key, $val){
        return setcookie(self::prefix($key), $val, time()+self::$expire, self::$path, self::$domain, self::$secure, self::$httponly);
    }

    /**
     * 获取变量魔术方法
     * @param  string $key 变量名
     * @return mixed       返回值
     */
    public function __get($key){
        if(isset($_COOKIE[self::prefix($key)])){
            return $_COOKIE[self::prefix($key)];
        }else{
            return null;
        }
    }

    public function set($key, $val, $expire=null, $path=null, $domain=null, $secure=null, $httponly=null){
        return setcookie(self::prefix($key), $val,
            time()+($expire ? $expire : self::$expire),
            $path ? $path : self::$path,
            $domain ? $domain : self::$domain,
            $secure ? $secure : self::$secure,
            $httponly ? $httponly : self::$httponly);
    }

    /**
     * 是否设置变量魔术方法
     * @param  string  $key 变量名
     * @return boolean      返回变量是否被设置
     */
    public function __isset($key){
        return isset($_COOKIE[self::prefix($key)]);
    }

    /**
     * 变量注销魔术方法
     * @param string $key 变量名
     */
    public function __unset($key){
        return setcookie(self::prefix($key), '', time());
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
     * 销毁cookie
     */
    public function destory(){
        //
    }

    /**
     * 返回所有session数据
     * @return mixed 返回session数据
     */
    public function all(){
        return $_COOKIE;
    }

}
