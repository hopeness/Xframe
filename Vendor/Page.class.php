<?php
namespace Core\Vendors;

/**
 * Page Class
 */

class Page{
    public $pageTotal;         // 总页数
    public $firstRow;            // 第一条记录位置
    public $pageSize;           // 每页显示个数
    public $nowPage;          // 当前页数
    public $resultTotal;        // 总结果数
    public $navigNum;        // 分页按钮显示个数

    private $pageIndex;       // 页数名称
    private $pageTitle;         // 页码导航
    private $pageURL;         // 分页链接

    /**
     * 初始化分页类
     * @param int $resultTotal  总结果数
     * @param int $pageSize  每页显示数量
     * @param string $pageIndex  页数索引名称
     * @param int $navigNum  分页条显示数量
     **/
    public function __construct($resultTotal,$pageSize = 10,$pageIndex = 'p',$navigNum = 9){
        $this->resultTotal = $resultTotal;
        $this->pageSize = intval($pageSize);
        $this->pageIndex = $pageIndex;
        $this->navigNum = $navigNum < 3 ? 3 : ceil($navigNum);
        if($this->pageSize < 1 || $this->pageSize > 10000){
            $this->pageSize = 10;
        }
        $this->pageTotal = ceil($this->resultTotal / $this->pageSize);
        if($this->pageTotal < 1){
            $this->pageTotal = 1;
        }
        $this->nowPage = isset($_GET[$pageIndex]) ? intval($_GET[$pageIndex]) : 1;
        if($this->nowPage < 1){
            $this->nowPage = 1;
        }
        if($this->nowPage > $this->pageTotal){
            $this->nowPage = $this->pageTotal;
        }
        $this->firstRow = $this->pageSize * ($this->nowPage - 1);
        $this->pageURL = $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?");
        $parse = parse_url($this->pageURL);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$this->pageIndex]);
            $this->pageURL = $parse['path'].'?'.http_build_query($params);
        }
    }
    
    /**
     * 分页导航条输出
     * @return string
     **/
    public function show(){
        if($this->pageTotal < 2){
            return '';
        }
        $navNum = ($this->pageTotal > $this->navigNum)?$this->navigNum:$this->pageTotal;
        $start = $this->nowPage - ceil(($this->navigNum - 1)/2);
        $start = $start < 1 ? 1 : $start;
        $start = ((($start + $this->navigNum) - 1) > $this->pageTotal)?1: $start;
        for($i = 1;$i <= $navNum;$i++){
            if($this->nowPage == $start){
                $this->pageTitle .= '<li class="active"><a>'.$start.'</a></li>';
            }else{
                $this->pageTitle .= '<li><a href="'.$this->pageURL.'&'.$this->pageIndex.'='.$start.'">'.$start.'</a></li>';
            }
            $start++;
        }
        return $this->pageTitle;
    }
    
    /**
     * 简洁导航条输出
     **/
     public function easyShow(){
        if($this->pageTotal < 2){
            return '';
        }
        if($this->nowPage == 1){
            $previous = '<a class="page_num_select"><</a>';
        }else{
            $previous = '<a href="'.$this->pageURL.'&'.$this->pageIndex.'='.($this->nowPage - 1).'" class="page_num"><</a>';
        }
        if($this->nowPage == $this->pageTotal){
            $next = '<a class="page_num_select">></a>';
        }else{
            $next = '<a href="'.$this->pageURL.'&'.$this->pageIndex.'='.($this->nowPage + 1).'" class="page_num">></a>';
        }
        return $previous.$next;
     }
}