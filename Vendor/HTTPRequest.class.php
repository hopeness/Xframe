<?php
namespace Core\Vendors;
defined('XFRAME') || exit('Access Denied');
/**
 * HTTP请求类
 */
class HTTPRequest{

    private $handle; // curl资源
    private $result; // 结果资源
    private $errno = null; // 请求返回码
    private $getInfo = null; // 资源信息
    private $overtime = 3; // 获取超时
    private $connecttimeout = 3; // 连接超时
    private $url; // url地址

    /**
     * HTTP请求类初始化
     */
    public function __construct(){
        // $this->init();
    }

    /**
     * GET方法
     * @param  string $url  url
     * @param  array  $data 数据
     * @return string       返回获取到的结果
     */
    public function get($url = '', $data = []){
        $this->url = $this->checkURL($url);
        if(empty($this->url)){
            return false;
        }
        $this->init();
        $this->setopt(CURLOPT_URL, $this->buildURL($this->url, $data));
        $this->setopt(CURLOPT_CUSTOMREQUEST, 'GET');
        $result = $this->exec();
        if($this->errno != 0){
            return false;
        }
        return $result;
    }

    /**
     * POST方法
     * @param  string $url  url
     * @param  array  $data 数据
     * @return string       返回获取到的结果
     */
    public function post($url = '', $data = []){
        $this->url = $this->checkURL($url);
        if(empty($this->url)){
            return false;
        }
        $this->init();
        $this->setopt(CURLOPT_URL, $this->url);
        $this->setopt(CURLOPT_CUSTOMREQUEST, 'POST');
        $this->setopt(CURLOPT_POSTFIELDS, $this->buildQuery($data));
        $result = $this->exec();
        if($this->errno != 0){
            return false;
        }
        return $result;
    }

    /**
     * DELETE方法
     * @param  string $url  url
     * @param  array  $data 数据
     * @return string       返回获取到的结果
     */
    public function delete($url, $data = []){
        $this->url = $this->checkURL($url);
        if(empty($this->url)){
            return false;
        }
        $this->init();
        $this->setopt(CURLOPT_URL, $this->url);//$this->buildURL($this->url, $data)
        $this->setopt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->setopt(CURLOPT_POSTFIELDS, $this->buildQuery($data));
        $result = $this->exec();
        if($this->errno != 0){
            return false;
        }
        return $result;
    }

    /**
     * PUT方法
     * @param  string $url  url
     * @param  array  $data 数据
     * @return string       返回获取到的结果
     */
    public function put($url, $data = []){
        $this->url = $this->checkURL($url);
        if(empty($this->url)){
            return false;
        }
        $this->init();
        $this->setopt(CURLOPT_URL, $this->buildURL($this->url, $data));
        $this->setopt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->setopt(CURLOPT_POSTFIELDS, $this->buildQuery($data));
        $result = $this->exec();
        if($this->errno != 0){
            return false;
        }
        return $result;
    }

    /**
     * 拼接query串
     * @param  array  $data 数据
     * @return string       返回拼接结果
     */
    private function buildQuery($data = []){
        $query = '';
        if(is_array($data)){
            $query = http_build_query($data);
        }else{
            $query = trim($data, '?...&');
        }
        return $query;
    }

    /**
     * 拼接url
     * @param  string $url  url
     * @param  array  $data 数据
     * @return string       返回拼接结果
     */
    private function buildURL($url = '', $data = []){
        $query = $this->buildQuery($data);
        if(strpos($this->url, '?')){
            $url .= '&'.$query;
        }else{
            $url .= '?'.$query;
        }
        return $url;
    }



    //------------------封装CURL函数-----------------//
    /**
     * [init description]
     * @return [type] [description]
     */
    private function init(){
        $this->handle = curl_init();
        $this->setopt(CURLOPT_HEADER, false);
        $this->setopt(CURLOPT_RETURNTRANSFER, true);
        $this->setopt(CURLOPT_TIMEOUT, $this->overtime);
        $this->setopt(CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
    }

    /**
     * [close description]
     * @return [type] [description]
     */
    private function close(){
        curl_close($this->handle);
        $this->init();
    }

    /**
     * [reset description]
     * @return [type] [description]
     */
    private function reset(){
        curl_reset($this->handle); // 内网45上curl版本没有重置函数。。。
    }

    /**
     * [exec description]
     * @return [type] [description]
     */
    private function exec(){
        $this->result = curl_exec($this->handle);
        $this->errno = curl_errno($this->handle);
        $this->getInfo = curl_getinfo($this->handle);
        $this->close();
        return $this->result;
    }

    /**
     * [setopt description]
     * @param  [type] $option [description]
     * @param  [type] $value  [description]
     * @return [type]         [description]
     */
    public function setopt($option, $value){
        curl_setopt($this->handle, $option, $value);
    }

    /**
     * [errno description]
     * @return [type] [description]
     */
    public function errno(){
        return $this->errno;
    }

    public function getInfo(){
        return $this->getInfo;
    }

    /**
     * [checkURL description]
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    private function checkURL($url){
        if(empty($url)){
            return false;
        }
        return $url;
    }
    //------------------封装结束-----------------//

    /**
     * 参数设置
     * @param string $key   键名
     * @param string $value 值
     */
    public function __set($key, $value){
        switch ($key) {
            case 'overtime':
            case 'timeout':
                $value = intval($value);
                if($value < 1){
                    return false;
                }
                $this->$key = $value;
                return true;

            default:
                return false;
                break;
        }
    }

    public function __destory(){
        $this->close();
    }

}
