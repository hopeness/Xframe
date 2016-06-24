<?php
namespace DB;
defined('XFRAME') || exit('Access Denied');
/**
 * 通用数据库操作类
 * 版本：v0.1
 * Author: Hopeness
 * Date: 2013-11-19
 * 支持：
 *     mysql、postgreSQL
 **/

class DB{

    // static private $obj = []; // 数据库资源
    private $obj; // 数据库资源

    static private $DB; // 本身资源

    static private $symbol = 'main'; // 数据库标识

    private $config = []; // 数据库配置

    private $sql = ''; // 最后执行的SQL语句

    private $result = null; // 最后返回的结果

    private $rows = 0; // 返回最后查询的结果行数

    private $is_master = false; // 是否所有操作都用主库

    private $rate = 0.5; // 从库切换命中率

    /**
     * DB类初始化操作
     * @param array $config   传入数据库配置
     * 配置解释：
     * array(
     *     '' => '',
     *     '' => ''
     * )
     **/
    private function __construct($config){
        $config['TYPE'] = isset($config['TYPE']) ? $config['TYPE'] : '';
        switch($config['TYPE']){
            case 'pgsql':
                require 'DB/PostgreSQL.class.php';
                $this->obj = new \DB\PostgreSQL($config);
                break;
            case 'mysql':
                require 'DB/MySQL.class.php';
                $this->obj = new \DB\MySQL($config);
                break;
            default:
                throw new \Exception('Your Database type is ERROR!');
                break;
        }
    }

    static public function getInstance($config, $symbol = 'main'){
        if(!isset(self::$DB[$symbol])){
            self::$symbol = $symbol;
            self::$DB[self::$symbol] = new self($config);
        }
        return self::$DB[$symbol];
    }

    // 方法传导调用
    public function __call($func, $argv){
        return call_user_func_array([$this->obj, $func], $argv);
    }

}
