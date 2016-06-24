<?php

namespace Xframe;

use \ReflectionClass,
    \ReflectionMethod,
    \Bootstrap,
    \Xframe\Dispather,
    \Xframe\Library\Routes,
    \Xframe\Exception;

final class Application {

    static private $URLS = false;
    
    public function __construct(){
        if(!defined('APP')){
            throw new Exception(_('Application of path is not defined'));
        }
        switch(PHP_SAPI){
            case 'cli':
                // CLI模式待测试
                define('APP_PATH', (strncmp(APP, '.', 1) === 0 ? realpath(realpath($argv[0]).'/'.APP) : realpath(APP)).'/');
                break;
            default:
                if(strncmp(APP, '/', 1) === 0){
                    $realPath = realpath(APP);
                }else{
                    $realPath = realpath(dirname($_SERVER['SCRIPT_FILENAME']).'/'.APP);
                }
                if($realPath){
                    define('APP_PATH', $realPath.'/');
                }else{
                    throw new Exception(_('APP is not extends!'));
                }
                break;
        }
        /**
         * Define some path of APP
         */
        define('MODEL_PATH', APP_PATH.'Model/'); // 模型文件目录
        define('VIEW_PATH', APP_PATH.'View/'); // 模型文件目录
        define('CONTROLLER_PATH', APP_PATH.'Controller/'); // 模型文件目录
        define('VENDOR_PATH', APP_PATH.'Vendor/'); // 应用公共文件目录
        define('COMMON_PATH', APP_PATH.'Common/'); // 应用公共文件目录
        define('LANGUAGE_PATH', VIEW_PATH.'Language/'); // 语言包路径

        /**
         * Set APP info
         */
        define('PATH', trim(C()->PATH, '/ '));
        define('STATIC_PATH', rtrim(C()->STATIC_PATH, '/ '));
        define('SKIN_NAME', C('default')->SKIN_NAME); // Set skin name
        define('SKIN_PATH', VIEW_PATH.SKIN_NAME.'/'); // Set APP skin PATH 

        define('HOST', $_SERVER['HTTP_HOST']); // Define host
        define('SCHEME', isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http'); // Define host
        define('SITE', SCHEME.'://'.HOST); // Define host
        define('STATIC_URL', SITE.'/Statics/'); // Define the path of public static file
        define('APP_STATIC_URL', SITE.'/'.STATIC_PATH.'/Statics/'); // Define the path of APP static
        define('SKIN_STATIC_URL', SITE.'/'.STATIC_PATH.'/View/'.SKIN_NAME.'/Statics/'); // Define the path of APP's skin static
    }

    public function bootstrap(){
        $dispather = Dispather::getInstance();
        if(is_file(COMMON_PATH.'Bootstrap.class.php')){
            require COMMON_PATH.'Bootstrap.class.php';
            if(class_exists('Bootstrap')){
                $bootstrap = new Bootstrap;
                $class = new ReflectionClass($bootstrap);
                $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
                foreach($methods as $key => $method){
                    if(strncmp($method->name, C('init')->BOTSTRAP_PREFIX, 4) == 0){
                        if(is_callable(array($bootstrap, $method->name))){
                            $bootstrap->{$method->name}($dispather);
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Start APP
     * @param string  $act   Controller path
     * @param boolean $index The twice iteration
     * @return
     **/
    public function run($controller = false, $index = false){
        $param = [];
        // 根据路径模式分发相应规则
        switch(C()->ROUTE){
            case 1: // 单独普通PATHINFO形式
                $controller = Routes::directory($controller);
                break;
            case 2: // 单独正则转发形式
                $controllerInfo = Routes::regEx($controller, self::$URLS);
                if($controllerInfo){
                    $controller = $controllerInfo[0];
                    $param = $controllerInfo[1];
                }else{
                    E(404);
                }
                break;
            case 3: // 两种都支持
                $controllerInfo = false;
                // 正则部分略过迭代
                if(!$index){
                    $controllerInfo = Routes::regEx($controller, self::$URLS);
                    if($controllerInfo){
                        $controller = $controllerInfo[0];
                        $param = $controllerInfo[1];
                    }
                }
                if(!$controllerInfo){
                    $controller = Routes::directory($controller);
                }
                break;
            default:
                throw new Exception(_('路径模式配置错误！'));
                break;
        }

        $controllerName = Routes::controllerName($controller);

        $controllerFile = CONTROLLER_PATH.$controller.'Controller'.CLASS_EXT;

        if(!is_file($controllerFile)){
            if(!$index && C()->ROUTE !== 2){
                self::run($controller.'/'.C()->DEFAULT_CONTROLLER, true);
                exit();
            }else{
                // 正则方式不进行迭代
                E(404);
            }
        }

        define('CONTROLLER', trim($controller, '/ '.C('/')->ROUTE_CUT));
        define('CONTROLLER_NAME', $controllerName);

        $className = $controllerName.'Controller';
        $controllerNamespace = str_replace('/', '\\', substr($controller, 0, strrpos($controller, '/')));
        $class = '\\Controller\\'.(empty($controllerNamespace) ? '' : $controllerNamespace.'\\').$className;

        if(!class_exists($class)){
            require $controllerFile;
        }

        if(!class_exists($class)){
            E(404);
            // throw new Exception(CL('Controller not found!'));
        }

        $app = new $class();
        
        if(is_callable([$app, 'construct'])){
            $app->construct();
        }
        if(is_callable([$app, 'main'])){
            call_user_func_array(array($app, 'main'), $param);
        }else{
            E(404);
        }
        if(is_callable([$app, 'destruct'])){
            $app->destruct();
        }
        return $this;
    }

    public function URLS($URLS = []){
        if(is_array($URLS) && !empty($URLS) && self::$URLS === false){
            self::$URLS = $URLS;
        }
    }

}
