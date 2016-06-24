<?php
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
    
    private $master; // 主库资源
    
    private $slave; // 从库资源
    
    private $master_config = []; // 当前使用的从库配置
    
    private $slave_config = []; // 当前使用的从库配置
    
    private $config = []; // 数据库配置
    
    private $sql = ''; // 最后执行的SQL语句
    
    private $result = null; // 最后返回的结果
    
    private $rows = 0; // 返回最后查询的结果行数
    
    private $is_master = false; // 是否所有操作都用主库
    
    private $rate; // 从库切换命中率
    
    /**
     * DB类初始化操作
     * @param array $config   传入数据库配置
     * 配置解释：
     * array(
     *     '' => '',
     *     '' => ''
     * )
     **/
    public function __construct($config){
        if(!is_array($config) || empty($config)) throw new \Exception('Your config is ERROR!');
        $this->config = $config;
        if(isset($this->config['master'])){
            $this->connect_matser();
            if(isset($this->config['slave']) && !empty($this->config['slave'])){
                if(isset($this->config['rate'])){
                    $this->rate = intval($this->config['rate']);
                }else{
                    $this->rate = 0;
                }
            }else{
                $this->is_master = true;
            }
        }else{
            throw new \Exception('Your DB config is ERROR!');
        }
    }
    
    private function connect_matser(){
        $this->master_config = $this->config['master'];
        switch($this->config['type']){
            case 'mysql':
                if(!isset($this->master_config['host']) || empty($this->master_config['host'])) $this->master_config['host'] = '127.0.0.1';
                if(!isset($this->master_config['port']) || empty($this->master_config['port'])) $this->master_config['port'] = 3306;
                if(!isset($this->master_config['dbname']) || empty($this->master_config['dbname'])) throw new \Exception('Master Database name is non-extends!');
                if(!isset($this->master_config['user']) || empty($this->master_config['user'])) $this->master_config['user'] = 'root';
                if(!isset($this->master_config['password']) || empty($this->master_config['password'])) $this->master_config['password'] = '';
                $opt = [PDO::ATTR_PERSISTENT => true];
                $this->master = new PDO('mysql:host='.$this->master_config['host'].';port='.$this->master_config['port'].';dbname='.$this->master_config['dbname'].';charset=utf8', $this->master_config['user'], $this->master_config['password'], $opt);
                break;
            case 'pgsql':
                if(!isset($this->master_config['host']) || empty($this->master_config['host'])) $this->master_config['host'] = '127.0.0.1';
                if(!isset($this->master_config['port']) || empty($this->master_config['port'])) $this->master_config['port'] = 5432;
                if(!isset($this->master_config['dbname']) || empty($this->master_config['dbname'])) throw new \Exception('Master Database name is non-extends!');
                if(!isset($this->master_config['user']) || empty($this->master_config['user'])) $this->master_config['user'] = 'postgres';
                if(!isset($this->master_config['password']) || empty($this->master_config['password'])) $this->master_config['password'] = '';
                #$opt = [PDO::ATTR_PERSISTENT => true];
                $this->master = new PDO('pgsql:host='.$this->master_config['host'].';port='.$this->master_config['port'].';dbname='.$this->master_config['dbname'], $this->master_config['user'], $this->master_config['password']);
                break;
            default:
                throw new \Exception('The Database type is ERROR!');
                break;
        }
    }
    
    private function connect_slave(){
        $this->slave_config = $this->config['slave'][array_rand($this->config['slave'])];
        switch($this->config['type']){
            case 'mysql':
                if(!isset($this->slave_config['host']) || empty($this->slave_config['host'])) $this->slave_config['host'] = '127.0.0.1';
                if(!isset($this->slave_config['port']) || empty($this->slave_config['port'])) $this->slave_config['port'] = 3306;
                if(!isset($this->slave_config['dbname']) || empty($this->slave_config['dbname'])) throw new \Exception('Slave Database name is non-extends!');
                if(!isset($this->slave_config['user']) || empty($this->slave_config['user'])) $this->slave_config['user'] = 'root';
                if(!isset($this->slave_config['password']) || empty($this->slave_config['password'])) $this->slave_config['password'] = '';
                $opt = [PDO::ATTR_PERSISTENT => true];
                $this->slave = new PDO('mysql:host='.$this->slave_config['host'].';port='.$this->slave_config['port'].';dbname='.$this->slave_config['dbname'].';charset=utf8', $this->slave_config['user'], $this->slave_config['password'], $opt);
            case 'pgsql':
                
                break;
            default:
                throw new \Exception('The Database type is ERROR!');
                break;
        }
    
    }
    
    /**
     * 返回最后执行的SQL语句
     **/
    public function lastSql(){
        return $this->sql;
    }
    
    /**
     * 返回最后插入的ID
     **/
    public function lastId(){
        return $this->master->lastInsertId();
    }
    
    /**
     * 返回最后查询的行数
     * 注：方法暂时有问题 rowCount函数只针对非select操作才行
     **/
    public function rows(){
        return $this->rows;
    }
    
    /**
     * 查询
     **/
    public function query($sql, $master = false){
        $this->sql = $sql;
        if($master || $this->is_master){
            $result = $this->master->query($this->sql);
            var_dump($result);
            exit();
        }else{
            if(empty($this->slave)){
                $this->connect_slave();
            }else{
                if($this->rate && rand($this->rate, 10) == 10){
                    $this->connect_slave();
                }
            }
            $result = $this->slave->query($this->sql);
        }
        if(empty($result)){
            $this->rows = 0;
            return false;
        }else{
            $this->rows = $result->rowCount();
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    /**
     * 获取所有结果集
     **/
    public function getAll($sql, $mode = false){
        return $this->query($sql);
    }
    
    /**
     * 获取一条数据
     **/
    public function getRow($sql, $mode = false){
        $result = $this->query($sql.' LIMIT 0, 1');
        if($result){
            $this->result = $result;
            return reset($this->result);
        }else{
            $this->result = null;
        }
    }
    
    /**
     * 获取一个字段的值
     **/
    public function getOne($sql, $mode = false){
        $result = $this->getRow($sql);
        if(is_array($result)){
            return reset($result);
        }else{
            return false;
        }
    }
    
    /**
     * 执行更新或插入语句
     **/
    public function exec($sql){
        $this->sql = $sql;
        return $this->master->exec($this->sql);
    }
    
    /**
     * 执行插入语句
     **/
    public function insert($sql){
        return $this->exec($sql);
    }
    
    /**
     * 执行更新语句
     **/
    public function update($sql){
        return $this->exec($sql);
    }
    
    /**
     * 快捷添加
     **/
    public function i($table, $data){
        $this->sql = $this->isql($table, $data);
        if(empty($this->sql)){
            return false;
        }
        return $this->exec($this->sql);
    }
    
    /**
     * 拼接添加语句
     **/
    public function isql($table, $data){
        if(empty($table) || !is_array($data)){
            return false;
        }
        $sql = $keys = $values = '';
        $first = true;
        $sql = 'INSERT INTO `'.$table.'` (';
        foreach($data as $key=>$val){
            if($first){
                $first = false;
                $keys .= '`'.$key.'`';
                $values .= '"'.$val.'"';
            }else{
                $keys .= ', `'.$key.'`';
                $values .= ', "'.$val.'"';
            }
        }
        $sql .= $keys.') VALUES('.$values.')';
        return $sql;
    }

    /**
     * 快捷更新
     **/
    public function u($table, $data, $where){
        $this->sql = $this->usql($table, $data, $where);
        if(empty($this->sql)){
            return false;
        }
        return $this->exec($this->sql);
    }
    
    /**
     * 拼接更新语句
     **/
    public function usql($table, $data, $where){
        if(empty($table) || !is_array($data) || empty($where)){
            return false;
        }
        $sql = '';
        $first = true;
        $sql = 'UPDATE `'.$table.'` SET ';
        foreach($data as $key=>$val){
            if($first){
                $first = false;
                $sql .= '`'.$key.'` = "'.$val.'"';
            }else{
                $sql .= ', `'.$key.'` = "'.$val.'"';
            }
        }
        $sql .= ' WHERE '.$where;
        return $sql;
    }
    
    /**
     * 快捷删除
     **/
    public function d($table = '', $where = ''){
        $this->sql = $this->dsql($table, $where);
        if(empty($this->sql)){
            return false;
        }
        return $this->exec($this->sql);
    }
    
    /**
     * 拼接更新语句
     **/
    public function dsql($table = '', $where = ''){
        if(empty($table) || empty($where)){
            return false;
        }
        $sql = 'DELETE FROM `'.$table.'` WHERE '.$where;
        return $sql;
    }
    
    /**
     * 执行删除语句
     **/
    public function delete($sql){
        return $this->exec($sql);
    }
    
    /**
     * 开启一个事物
     **/
    public function beginTrans(){
        return $this->master->beginTransaction();
    }
    
    /**
     * 提交事务
     **/
    public function commit(){
        return $this->master->commit();
    }
    
    /**
     * 判断是否在一个事物中
     **/
    public function inTrans(){
        return $this->master->inTransaction();
    }
    
    /**
     * 事务回滚操作
     **/
    public function rollBack(){
        return $this->master->rollBack();
    }
    
    public function getConfig(){
        return $this->config;
    }
    
}