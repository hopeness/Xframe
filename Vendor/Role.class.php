<?php
namespace Core\Vendors;
defined('XFRAME') || exit('Access Denied');

/**
 * Rule 权限操作类
 **/
 
class Rule{
    
    private $db;
    
    /**
     * _construct 初始化
     * 
     **/
    public function __construct(){
        global $db;
        $this->db = $db;
    }
    
    /**
     * addNode 添加
     * @param array $data 传入节点信息
     * @return boolean 返回执行是否成功
     **/
    function addNode($data){
        $this->beginTrans();
        unset($data['mid']);
        $this->i('rule_node', $data);
        $this->commitTrans();
        if($this->hasFailedTrans()){
            $this->rollbackTrans();
            return false;
        }else{
            return true;
        }
    }
    
    /**
     * editNode 修改节点信息
     * @param array $data 传入节点信息，必须包含mid
     * @return boolean 返回执行知否成功
     **/
    function editNode($data){
        if(!isset($data['mid'])){
            return false;
        }
        $mid = $data['mid'];
        if(!$this->getNodeInfo($mid)){
            return false;
        }
        unset($data['mid']);
        
        $this->beginTrans();
        $this->U('rule_node', $data, '`mid` = '.$mid.' AND `isdel` = "0"');
        $this->commitTrans();
        if($this->hasFailedTrans()){
            $this->rollbackTrans();
            return false;
        }else{
            return true;
        }
    }
    
    /**
     * delNode 删除节点
     * @param int $mid 传入要删除的mid
     * @param boolean $batch 是否批量处理
     * @return boolean 是否执行成功
     **/
    public function delNode($mid, $batch = false){
        if($batch){
            $menuInfo = true;
        }else{
            $menuInfo = $this->getNodeInfo($mid);
        }
        if($menuInfo){
            $this->beginTrans();
            $this->U('rule_node', array('isdel' => '1'), '`mid` in('.$mid.')');
            if($this->hasFailedTrans()){
                $this->rollbackTrans();
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }
    
    /**
     * del
     * 
     **/
    
    /**
     * checkMenu 验证节点
     * @param array $data 传入节点信息数组
     * @return array 返回验证信息，正确则为空数组
     **/
    public function checkNode($data){
        $warn = array();
        // 数据验证
        if(empty($data['mname']) || strlen($data['mname']) > 50){
            $warn['type'] = 3;
            $warn['error']['mname'] = '请正确填写节点名！';
        }
        if(!preg_match('/^[a-z0-9._-]{1,255}$/i', $data['module'])){
            $warn['type'] = 3;
            $warn['error']['module'] = '请正确填写模块！';
        }
        // 获取菜单信息
        global $menu;
        $menuinfo = $menu->getMenuInfo($data['menu']);
        if(empty($menuinfo) || $menuinfo['level'] != 3){
            $warn['type'] = 3;
            $warn['error']['menu'] = '请正确选择菜单!';
        }
        if(strlen($data['note']) > 255){
            $warn['type'] = 3;
            $warn['error']['note'] = '注释过长！';
        }
        if(!in_array($data['status'], array(0, 1))){
            $warn['type'] = 2;
            $warn['msg'] = '请正确选择状态！';
        }
        return $warn;
    }
    
    /**
     * getNodeInfo 获取节点信息
     * @param int $mid 传入节点id
     * @return array 返回数据
     **/
    public function getNodeInfo($mid){
        return $this->getRow('SELECT * FROM `'.p('rule_node').'` WHERE `isdel` = "0" AND `mid` = '.$mid);
    }
     
    /**
     * nodeList 节点列表
     * @return array 返回数据
     **/
    public function nodeList(){
        return $this->getAll('SELECT n.*, m.`mname` AS `menuname` FROM `'.p('rule_node').'` AS n LEFT JOIN `'.p('menu').'` AS m ON n.`menu` = m.`mid` AND m.`isdel` = "0" WHERE n.`isdel` = "0" ORDER BY n.`module` ASC');
    }
    
    
    
}