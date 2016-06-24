<?php
use Xframe\App,
    Xframe\Library\Config;

/**
 * url 拼装url地址
 * @param string $controller Controller
 * @param string $queryParam 查询串
 * @param string 拼装后的url地址
 **/
function U($controller = '', $queryParam = ''){
    return \Xframe\Core\Routes::U($controller, $queryParam);
}

function V($model, $in = ''){
    static $vendors = [];
    $file = $model.((strcasecmp(substr($model, -4), '.php') !== 0) ? '.class.php' : '');
    if(!isset($vendors[$in.'|'.$file]) || !$vendors[$in.'|'.$file]){
        $vendors[$in.'|'.$file] = null;
    }
    $requireFile = '';
    switch(strtolower($in)){
        case 1:
        case 'core':
            $requireFile = XFRAME_VENDOR_PATH.$file;
            break;
        case 2:
        case 'root':
            $requireFile = ROOT_VENDOR_PATH.$file;
            break;
        case 3:
        case 'app':
            $requireFile = VENDOR_PATH.$file;
            break;
        default:
            if(is_file(VENDOR_PATH.$file)){
                $requireFile = VENDOR_PATH.$file;
            }elseif(is_file(ROOT_VENDOR_PATH.$file)){
                $requireFile = ROOT_VENDOR_PATH.$file;
            }elseif(is_file(XFRAME_VENDOR_PATH.$file)){
                $requireFile = XFRAME_VENDOR_PATH.$file;
            }
            break;
    }
    if($requireFile && is_file($requireFile)){
        include $requireFile;
        $vendors[$in.'|'.$file] = true;
        return true;
    }else{
        throw new Exception(_('Can\'t find vendor!'));
        $vendors[$in.'|'.$file] = false;
        return false;
    }
}

/**
 * p 自动添加表前缀
 * @param string $table 传入表名
 * @return string 返回添加前缀的表名
 **/
function P($table){
    return C('DB')['PREFIX'].$table;
}

/**
 * 随机CDN函数
 * @param string $param CDN根路径
 * @param int    $num   固定CDN路径
 */
function CDN($param, $num = false){
    if(empty(C('CDN')) || C('IGNORE_CDN')){
        return $param;
    }else{
        if(is_array(C('CDN'))){
            if(is_numeric($num)){
                $num = intval($num);
                $_CDN = C('CDN')[isset(C('CDN')[$num]) ? $num : 0];
            }else{
                $_CDN = C('CDN')[array_rand(C('CDN'))];
            }
        }else{
            $_CDN = C('CDN');
        }
        switch($param){
            case HOST:
                return $_CDN;
                break;
            case STATIC_URL:
                return $_CDN.'/Statics/';
                break;
            case APP_STATIC_URL:
                return $_CDN.'/'.STATIC_PATH.'/View/Statics/';
                break;
            case SKIN_STATIC_URL:
                return $_CDN.'/'.STATIC_PATH.'/View/'.SKIN_NAME.'/Statics/';
                break;
            default:
                return $_CDN;
                break;
        }
    }
}

/**
 * 随机LIB函数
 * @param string $param CDN根路径
 * @param int    $num   固定CDN路径
 */
function LIB($num = false){
    if(empty(C('LIB'))){
        return false;
    }else{
        if(is_array(C('LIB'))){
            if($num === false){
                $LIB = C('LIB')[array_rand(C('LIB'))];
            }else{
                $num = intval($num);
                $LIB = C('LIB')[isset(C('LIB')[$num]) ? $num : 0];
            }
        }else{
            $LIB = C('LIB');
        }
    }
    return $LIB;
}

/**
 * 模型创建方法
 * @param string $model 模型名称
 **/
function M($model = false){
    if(!$model){
        $model = CONTROLLER;
    }
    $model = trim($model, '/ ');
    $modelName = ((substr_count($model, '/') > 0) ? substr($model, strrpos($model, '/') + 1) : $model).'Model';
    $modePath = substr($model, 0, strrpos($model, '/'));
    $modelFile = APP_PATH.'Model/'.$modePath.'/'.$modelName.CLASS_EXT;
    $class = '\\Model\\'.(empty($modePath) ? '' : str_replace('/', '\\', $modePath).'\\').$modelName;

    if(!is_file($modelFile)){
        throw new Exception(CL('MODULE_NOT_EXIST'));
    }

    if(!class_exists($class)){
        include $modelFile;
    }

    if(!class_exists($class)){
        throw new Exception(CL('MODULE_NOT_EXIST'));
    }
    $model_obj = new $class();
    if(method_exists($model_obj, 'init')){
        $model_obj->init();
    }
    return $model_obj;
}

/**
 * 公共方法
 **/

/**
 * filter 危险字符过滤函数
 * @param string $string 输入字符串
 * @param int $html 是否转入html实体
 * @return string 返回安全的字符串
 **/
function filter($string, $html = 0){
    if(!is_string($string)){
        return $string;
    }
    $string = trim(addslashes($string));
    if($html){
        $string = htmlspecialchars($string, ENT_QUOTES);
    }
    return $string;
}

/**
 * cut 截取字符串 
 * @param string $str 要截取的字符串，默认""
 * @param int $length 截取的长度，默认10
 * @param int $start 长度，默认0
 * @param string $ellipsis 省略号，默认为"..."如果不需要则填""
 * @return string 返回字符串
 **/
function cut($str = '', $length = 10, $start = 0, $ellipsis = '...'){
    $sub_str = '';
    $sub_str = mb_substr($str, $start, $length, 'utf-8');
    if(mb_strlen($str, 'utf-8') > $length){
        $sub_str .= $ellipsis;
    }
    return $sub_str;
}

/**
 * fullToHalf 全角转半角函数
 * @param string $str 传入要处理的字符串
 * @return string 返回处理后的字符
 **/
function fullToHalf($str){
    $full = ['　', '０', '１', '２', '３', '４', '５', '６', '７', '８', '９', 'ａ', 'ｂ', 'ｃ', 'ｄ', 'ｅ', 'ｆ', 'ｇ', 'ｈ', 'ｉ', 'ｊ', 'ｋ', 'ｌ', 'ｍ', 'ｎ', 'ｏ', 'ｐ', 'ｑ', 'ｒ', 'ｓ', 'ｔ', 'ｕ', 'ｖ', 'ｗ', 'ｘ', 'ｙ', 'ｚ', 'Ａ', 'Ｂ', 'Ｃ', 'Ｄ', 'Ｅ', 'Ｆ', 'Ｇ', 'Ｈ', 'Ｉ', 'Ｊ', 'Ｋ', 'Ｌ', 'Ｍ', 'Ｎ', 'Ｏ', 'Ｐ', 'Ｑ', 'Ｒ', 'Ｓ', 'Ｔ', 'Ｕ', 'Ｖ', 'Ｗ', 'Ｘ', 'Ｙ', 'Ｚ'];
    $half = [' ', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    return str_replace($full, $half, $str);
}

/**
 * method 返回客户端请求方式
 * @return blooean
 **/
function method($method){
    if(strcasecmp($_SERVER['REQUEST_METHOD'], $method) == 0){
        return true;
    }else{
        return false;
    }
}

/**
 * isAJAX 是否是AJAX提交
 * @return boolean
 **/
function isAJAX(){
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'xmlhttprequest') == 0){
        return true;
    }else{
        return false;
    }
}

/**
 * random 随机密码
 * @param int $type 密码类型，默认 1 数字，2 字母，3 数字和字母， 4 数字、字母、符号
 * @param int $length 密码长度，默认为6
 * @return string 返回随机字符串
 */
function random($type = 1, $length = 6){
    $random = '';
    switch($type){
        case 1:
            $random = str_pad(rand(0, (pow(10, $length) - 1)), $length, '0', STR_PAD_LEFT);
            break;
        case 2:
            $value = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            for($i=0; $i<$length; $i++){
                $random .= $value[rand(0, 51)];
            }
            break;
        case 3:
            $value = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            for($i=0; $i<$length; $i++){
                $random .= $value[rand(0, 61)];
            }
            break;
        case 4:
            $value = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()?<>,.;:[]|/\-_=+{}';
            for($i=0; $i<$length; $i++){
                $random .= $value[rand(0, 89)];
            }
            break;
        default:
            $random = 'error';
            break;
    }
    return $random;
}

/**
 * 获取客户端真实ip
 * @return 返回用户真实IP
 */
function IP(){
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return $ip;
}


/**
 * 获取配置
 */
function C($default = null){
    return Config::setDefault($default);
}

/**
 * 框架核心语言包
 **/
function CL($name = null){
    static $_LANG = [];
    if(empty($name)) return $_lang;
    // 限制只能内核初始化时设置一次预言包
    if(is_array($name) && empty($_LANG)){
        $_LANG = array_merge($_LANG, array_change_key_case($name, CASE_UPPER));
    }
    if(is_string($name)){
        if(isset($_LANG[$name])){
            return $_LANG[$name];
        }else{
            return false;
        }
    }
    return true;
}


/**
 * 格式化输出
 * @param all type $value 要格式化输出的值
 * @return string 返回结果
 **/
function dump($value, $full = false){
    echo '<pre>';
    if($full){
        var_dump($value);
    }else{
        print_r($value);
    }
    echo '</pre>';
}

/**
 * 错误处理函数
 **/
function E($code = false, $data = []){
    switch($code){
        case 404:
            header('HTTP/1.1 404 Not Found');
            $errorFile = APP_PATH.'View/'.SKIN_NAME.'/Common/404'.VIEW_EXT;
            if(is_file($errorFile)){
                include $errorFile;
            }else{
                // Require systerm 404 file;
            }
            break;
        case 500:
            break;
        case 'system_error':
            dump(debug_backtrace());
            // todo: 完善
            $errorFile = APP_PATH.'View/'.SKIN_NAME.'/error.php';
            if(is_file($errorFile)){
                include $errorFile;
            }else{
                include X_COMMON_PATH.'tpl/error.php';
            }
            break;
        default:
            echo $msg;
            break;
    }
    exit();
}
