<?php

namespace Xframe;
use Xframe\RequestAbstract,
    Xframe\Routernterface;

final class Router
{

    static private $instance;

    private $request;
    private $controller;
    private $routers = [];

    static public function getInstance(RequestAbstract $request): self
    {
        if(!(self::$instance instanceof self))
        {
            self::$instance = new self($request);
        }
        return self::$instance;
    }

    private function __construct(RequestAbstract $request)
    {
        $this->request = $request;
        
    }

    public function addRouter($name, RouteInterface $router): bool
    {
        $this->routers[$name] = $router;
        return true;
    }

    public function delRouter($name)
    {
        unset($this->routers[$name]);
    }

    public function route(): bool
    {
        $routeRet = false;
        foreach($this->routers as $router){
            $ret = $router->route($this->request);
            if($ret){
                $routeRet = true;
                break;
            }
        }
        $this->request->routed();
        return $routeRet;
    }














    /**
     * Make url
     * @return string
     **/
    public static function U($controller = '', $queryParam = ''){
        $controller = self::controllerHandle($controller);
        if(is_array($queryParam)){
            $query = http_build_query($queryParam);
        }else{
            $query = $queryParam;
        }
        $url = SITE.str_replace('//', '/', '/'.trim(PATH).'/');
        if(C('FORCE_STATIC')){
            $url .= $controller == C()->DEFAULT_CONTROLLER ? '' : $controller;
        }else{
            $url .= $controller == C()->DEFAULT_CONTROLLER ? '' : C()->DEFAULT_INDEX_FILE.'/'.$controller;
        }
        $url .= empty($query) ? '' : '?'.$query;
        return $url;
    }

    /**
     * Get requiest URI
     * @return string
     **/
    static public function requiestURI(){
        if(!self::$requiestURI){
            self::$requiestURI = substr($_SERVER['REQUEST_URI'], strlen(in_array(dirname($_SERVER['SCRIPT_NAME']), array('/', '\\')) ? '' : dirname($_SERVER['SCRIPT_NAME'])));
            if(substr(self::$requiestURI, 0, strlen(C()->DEFAULT_INDEX_FILE) + 2) == '/'.C()->DEFAULT_INDEX_FILE.'/'){
                self::$requiestURI = substr(self::$requiestURI, strlen(C()->DEFAULT_INDEX_FILE) + 1);
            }
            $questionMark = strpos(self::$requiestURI, '?');
            if($questionMark !== false){
                self::$requiestURI = substr(self::$requiestURI, 0, $questionMark);
            }
            if(empty(self::$requiestURI)){
                self::$requiestURI = '/';
            }
        }
        return self::$requiestURI;
    }

    /**
     * Action arrangement
     * @param  string $controller  Controller
     * @param  mixed  $cut             The symbol of cut
     * @param  bool   $orign           是否保持原pathinfo样式(是否去除首尾斜线，便于正则匹配)
     * @return string Return           act
     **/
    static public function controller(){
        if(!self::$controller){
            self::$controller = self::controllerHandle(self::requiestURI());
        }
        return self::$controller;
    }

    static public function controllerHandle($controller = false, $cut = '/'){
        $controller = trim($controller, '/ '.$cut);
        if(empty($controller)) $controller = C()->DEFAULT_CONTROLLER;
        if(strcmp($cut, '/') === 0) return $controller;
        return preg_replace('#/+#', '/', str_replace($cut, '/', $controller));
    }

    /**
     * Action path arrangement
     * @param string  $controller   controller
     * @return string controller name
     **/
    static public function controllerName($controller){
        return trim(substr($controller, strrpos($controller, '/')), '/ '.C('/')->ROUTE_CUT);
    }

    /**
     * 默认文件目录默认路由处理
     * @param  [type] $controllerPath [description]
     * @return [type]                 [description]
     */
    static public function directory($controller = false){
        if(!$controller){
            $controller = self::controller();
        }
        return self::controller($controller);
    }

    /**
     * 正则形式路由处理
     * @param  string $requiestURI    控制器名
     * @param  array  $URLS            规则
     * @return mix                    返回匹配到的控制器和参数
     */
    static public function regEx($requiestURI = '', $URLS = []){
        if(!$requiestURI){
            $requiestURI = self::requiestURI();
        }
        if(is_array($URLS)){
            foreach($URLS as $pathRule => $controller){
                if(preg_match('#^'.$pathRule.'$#i', $requiestURI, $param)){
                    array_shift($param);
                    return [self::controllerHandle($controller), $param];
                    break;
                }
            }
        }
        return false;
    }

}
