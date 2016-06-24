<?php
/**
 * Xframe is a opensource php framework
 * =========================================
 * vendor DB
 * =========================================
 * @author     Hopeness <houpengg@gmail.com>
 * @link       https://github.com/hopeness/Xframe
 * @version    0.0.1 beta
 * @category   Core
 * @package    Vendor
 * @copyright  Copyright (c) 2013-2014 Hopeness (http://www.hopeness.net)
 * @license    https://github.com/hopeness/Xframe/blob/master/LICENSE  GNU License
 */

namespace DB;

/**
 * PostgreSQL类
 * 版本：v0.1
 * Author: Hopeness
 * Date: 2013-11-19
 * 支持：
 *     mysql、postgreSQL
 **/
class PostgreSQL{

    private $master = null; // 主库资源

    private $slave = null; // 从库资源

    private $slaveNumber = 0; // 从库个数

    private $allConfig = []; // 所有数据库配置

    private $config = []; // 当前数据库配置

    private $sql = ''; // 最后执行的SQL语句

    private $result = null; // 最后返回的结果

    private $rows = 0; // 返回最后查询的结果行数

    private $rate; // 从库切换命中率

    private $ORM_SQL = []; // ORM方法SQL变量

    /**
     * DB类初始化操作
     * @param array $config   传入数据库配置
     * 配置解释：
     * array(
     *     '' => '',
     *     '' => ''
     * )
     **/
    public function __construct($allConfig){
        $this->allConfig = $allConfig;
        if(!isset($this->allConfig['MASTER'])){
            throw new \PDOException(_('MASTER config is empty!'));
        }
        $this->connectMatser();
        if(!isset($this->allConfig['SLAVE']) || empty($this->allConfig['SLAVE'])){
            $this->slaveNumber = 0;
        }else{
            $this->slaveNumber = count($this->allConfig['SLAVE']);
        }
        $this->connectSlave();
    }

    /**
     * 连接主库
     * @param array $config 主库配置
     * @return
     */
    private function connectMatser(){
        // 判断是否存在指定的数据库实例
        $this->config['MASTER'] = $this->checkConfig($this->allConfig['MASTER']);
        try{
            $this->master = new \PDO('pgsql:host='.$this->config['MASTER']['HOST'].';port='.$this->config['MASTER']['PORT'].';dbname='.$this->config['MASTER']['DBNAME'],
                $this->config['MASTER']['USER'],
                $this->config['MASTER']['PASSWORD'],
                $this->connectOption()
                );
        }catch(PDOException $e){
            throw new \PDOException(_('MASTER connect failed!'));
        }
    }

    /**
     * 连接从库
     * @param array $config 从库配置
     * @return
     */
    private function connectSlave(){
        // 多从库可随机选择连接
        if($this->slaveNumber > 0){
            $this->config['SLAVE'] = $this->checkConfig($this->allConfig['SLAVE'][rand(0, $this->slaveNumber - 1)], 'slave');
            $this->rate = $this->config['SLAVE']['RATE'];
            try{
                $this->slave = new \PDO('pgsql:host='.$this->config['SLAVE']['HOST'].';port='.$this->config['SLAVE']['PORT'].';dbname='.$this->config['SLAVE']['DBNAME'],
                    $this->config['SLAVE']['USER'],
                    $this->config['SLAVE']['PASSWORD'],
                    $this->connectOption()
                    );
            }catch(PDOException $e){
                throw new \PDOException(_('SLAVE connect failed!'));
            }
        }else{
            $this->slave = $this->master;
        }
    }

    /**
     * 设置默认连接参数
     */
    private function connectOption(){
        return [
            // \PDO::ATTR_PERSISTENT => true, // 持久连接
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, // 错误触发异常
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC // 返回索引结果集
            ];
    }

    /**
     * 检查PostgreSQL配置，并对空值设置默认值
     * @param  [type] $config [description]
     * @return [type]         [description]
     */
    private function checkConfig($config, $type = 'master'){
        if(!isset($config['HOST']) || empty($config['HOST']))
            throw new \PDOException(_('HOST is empty of DB config!'));
        if(!isset($config['PORT']) || empty($config['PORT']))
            $config['PORT'] = 5432;
        if(!isset($config['DBNAME']) || empty($config['DBNAME']))
            throw new \Exception(_('DBNAME name is non-extends!'));
        if(!isset($config['USER']) || empty($config['USER']))
            throw new \PDOException(_('USER is empty of DB config!'));
        if(!isset($config['PASSWORD']) || empty($config['PASSWORD']))
            throw new \PDOException(_('PASSWORD is empty of DB config!'));
        if($type == 'slave'){
            if(!isset($config['RATE']) || empty($config['RATE'])){
                $config['RATE'] = 5;
            }
        }
        return $config;
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
    public function lastInsertId($sequence){
        return $this->master->lastInsertId($sequence);
    }

    /**
     * 返回最后查询的行数
     * 注：方法暂时有问题 rowCount函数只针对非select操作才行
     **/
    public function rows(){
        return $this->rows;
    }

    /**
     * 查询操作
     * @param  [type]  $sql    [description]
     * @param  boolean $master [description]
     * @return [type]          [description]
     */
    public function query($sql, $master = false){
        $this->sql = $sql;
        if($this->slaveNumber == 0 || $master){
            $result = $this->master->query($this->sql);
        }else{
            if($this->rate && $this->slaveNumber > 1 && rand($this->rate, 10) == 10){
                $this->connectSlave();
            }
            $result = $this->slave->query($this->sql);
        }
        return $result->fetchAll();
    }

    /**
     * 执行更新或插入语句
     **/
    public function exec($sql){
        $this->sql = $sql;
        return $this->master->exec($this->sql);
    }

    /**
     * SQL预处理
     * @param  string  $statement      SQL模板
     * @param  array   $driver_options 驱动选项
     * @param  bool $master         是否使用主库查询
     * @return PDOStatement            返回PDO资源对象
     */
    public function prepare($statement, $driver_options = [], $master = false){
        $statement = trim($statement);
        switch($master ? 'MASTER' : strtoupper(substr($statement, 0, strpos($statement, ' ')))){
            case 'SELECT':
                return $this->slave->prepare($statement, $driver_options);
                break;
            default:
                return $this->master->prepare($statement, $driver_options);
                break;
        }
    }

    /**
     * 获取所有结果集
     * @param  string  $sql    SQL语句
     * @param  bool $master 是否使用主库查询
     * @return array           返回结果数组
     */
    public function getAll($sql, $master = false){
        return $this->query($sql, $master);
    }

    /**
     * 获取一条数据
     * @param  string  $sql    SQL语句
     * @param  bool $master 是否使用主库查询
     * @return array           返回结果数组
     */
    public function getRow($sql, $master = false){
        $result = $this->query($sql.' LIMIT 1', $master);
        return reset($result);
    }

    /**
     * 获取一个字段的值
     * @param  string  $sql    SQL语句
     * @param  bool $master 是否使用主库查询
     * @return string          返回查询结果
     */
    public function getOne($sql, $master = false){
        $result = $this->getRow($sql, $master);
        return is_array($result) ? reset($result) : $result;
    }

    /************ ORM ************/

    public function field($field = '*'){
        $this->ORM_SQL['field'] = $field;
        return $this;
    }

    public function from($table = ''){
        $this->ORM_SQL['table'] = $table;
        return $this;
    }

    public function leftJoin($leftJoin = ''){
        $this->ORM_SQL['leftJoin'] = $leftJoin;
        return $this;
    }

    public function rightJoin($rightJoin = ''){
        $this->ORM_SQL['rightJoin'] = $rightJoin;
        return $this;
    }

    public function where($where = ''){
        $this->ORM_SQL['where'] = $where;
        return $this;
    }

    public function order($order = ''){
        $this->ORM_SQL['order'] = $order;
        return $this;
    }

    public function limit($start = 0, $offset = 0){
        $this->ORM_SQL['limit'] = 'LIMIT '.$start.' OFFSET '.$offset;
        return $this;
    }

    public function select(){
        $sql = 'SELECT';
        // field
        $sql .= isset($this->ORM_SQL['field']) ? ' '.$this->ORM_SQL['field'] : '*';
        // table
        if(!isset($this->ORM_SQL['table'])){
            throw new \PDOException(_('Have no table select!'));
        }else{
            $sql .= ' FROM '.$this->ORM_SQL['table'];
        }
        // where
        $sql .= isset($this->ORM_SQL['where']) ? ' WHERE '.$this->ORM_SQL['where'] : '';
        // order
        $sql .= isset($this->ORM_SQL['order']) ? ' ORDER BY '.$this->ORM_SQL['order'] : '';
        // limit
        $sql .= isset($this->ORM_SQL['limit']) ? ' '.$this->ORM_SQL['limit'] : '';
        $sql .= ';';
        return $this->query($sql);
    }


    /**
     * 快捷添加
     * @param  string $table 表名
     * @param  array  $data  要插入的数据
     * @return int           影响结果行数
     */
    public function I($table, $data){
        $this->sql = $this->ISQL($table, $data);
        if(empty($this->sql)){
            return false;
        }
        return $this->exec($this->sql);
    }

    /**
     * 生成INSERT语句
     * @param  string $table 表名
     * @param  array  $data  要插入的数据
     * @return string        生成SQL语句
     */
    public function ISQL($table, $data){
        if(empty($table) || !is_array($data)){
            return false;
        }
        $sql = $keys = $values = '';
        $first = true;
        $sql = 'INSERT INTO '.P($table).' (';
        foreach($data as $key=>$val){
            if($first){
                $first = false;
                $keys .= $key;
                $values .= '\''.$val.'\'';
            }else{
                $keys .= ', '.$key;
                $values .= ', \''.$val.'\'';
            }
        }
        $sql .= $keys.') VALUES('.$values.'); ';
        return $sql;
    }

    /**
     * 快捷更新
     * @param  string $table 表名
     * @param  array  $data  要修改的数据
     * @param  string $where 修改条件
     * @return int           影响结果行数
     */
    public function U($table, $data, $where){
        $this->sql = $this->USQL($table, $data, $where);
        if(empty($this->sql)){
            return false;
        }
        return $this->exec($this->sql);
    }

    /**
     * 生成UPDATE语句
     * @param  string $table 表名
     * @param  array  $data  要修改的数据
     * @param  string $where 修改条件
     * @return string        生成SQL语句
     */
    public function USQL($table, $data, $where){
        if(empty($table) || !is_array($data) || empty($where)){
            return false;
        }
        $sql = '';
        $first = true;
        $sql = 'UPDATE '.P($table).' SET ';
        foreach($data as $key=>$val){
            if($first){
                $first = false;
                $sql .= $key.' = \''.$val.'\'';
            }else{
                $sql .= ', '.$key.' = \''.$val.'\'';
            }
        }
        $sql .= ' WHERE '.$where.'; ';
        return $sql;
    }

    /**
     * 执行删除语句
     **/
    public function delete($sql){
        return $this->exec($sql);
    }

    /**
     * 快捷删除
     * @param  string $table 表名
     * @param  string $where 删除条件
     * @return int           影响结果行数
     */
    public function D($table = '', $where = ''){
        $this->sql = $this->DSQL($table, $where);
        if(empty($this->sql)){
            return false;
        }
        return $this->exec($this->sql);
    }

    /**
     * 生成DELETE语句
     * @param  string $table 表名
     * @param  string $where 删除条件
     * @return string        生成SQL语句
     */
    public function DSQL($table = '', $where = ''){
        if(empty($table) || empty($where)){
            return false;
        }
        $sql = 'DELETE FROM '.P($table).' WHERE '.$where.'; ';
        return $sql;
    }

    /**
     * 开启一个事物
     **/
    public function beginTransaction(){
        return $this->master->beginTransaction();
    }

    /**
     * 提交事务
     **/
    public function commit(){
        return $this->master->commit();
    }

    /**
     * 判断是否在事务中
     **/
    public function inTransaction(){
        return $this->master->inTransaction();
    }

    /**
     * 事务回滚
     **/
    public function rollBack(){
        return $this->master->rollBack();
    }

    /**
     * 获取跟数据库句柄上一次操作相关的 SQLSTATE
     * @param  bool $master 是否使用主库
     * @return mixed
     */
    public function errorCode($master = false){
        if($master){
            return $this->master->errorCode();
        }else{
            return $this->slaveNumber ? $this->slave->errorCode() : null;
        }
    }

    /**
     * 获取跟数据库句柄上一次操作相关的错误信息
     * @param  bool $master 是否使用主库
     * @return mixed
     */
    public function errorInfo($master = false){
        if($master){
            return $this->master->errorInfo();
        }else{
            return $this->slaveNumber ? $this->slave->errorInfo() : null;
        }
    }

    /**
     * 取回一个数据库连接的属性
     * @param  int     $attribute 应用到数据库连接中的常量
     * @param  bool $master    是否使用主库
     * @return mixed
     */
    public function getAttribute($int, $master = false){
        if(strcasecmp($type, 'slave') == 0){
            return $this->slaveNumber ? $this->slave->getAttribute($attribute) : null;
        }else{
            return $this->master->getAttribute($attribute);
        }
    }

    /**
     * 返回一个可用驱动的数组
     * @param  bool  $master    是否使用主库
     * @return array
     */
    public function getAvailableDrivers($master = false){
        if($master){
            return $this->master->getAvailableDrivers();
        }else{
            return $this->slaveNumber ? $this->slave->getAvailableDrivers() : null;
        }
    }

    /**
     * 设置属性
     * @param  bool  $master    是否使用主库
     * @param  int   $attribute 属性
     * @param  mixed $attribute 属性值
     * @return bool
     */
    public function setAttribute($attribute, $value, $type = 'master'){
        if(strcasecmp($type, 'slave') == 0){
            return $this->slaveNumber ? $this->slave->setAttribute($attribute, $value) : null;
        }else{
            return $this->master->setAttribute($attribute, $value);
        }
    }

    /**
     * 禁止克隆
     */
    public function __clone(){
        throw PDOException(_('Clone is not allow!'));
    }

    /**
     * 析构
     */
    public function __destruct(){
        // var_dump($this->qu);
    }

}
