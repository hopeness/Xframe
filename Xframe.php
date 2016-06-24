<?php
/**
 * Xframe
 * @author     Peter <houpengg@gmail.com>
 * @link       https://github.com/hopeness/Xframe
 * @license    https://github.com/hopeness/Xframe/blob/master/LICENSE GNU License
 */

/**
 * 强类型判断
 */
declare(strict_types=1);

use Xframe\Dispatcher,
    Xframe\Loader,
    Xframe\Application,
    Xframe\Router,
    Xframe\RouteSimple,
    Xframe\RequestHttp,
    Xframe\ResponseHttp,
    Xframe\BootstrapAbstract;

/**
 * Xframe框架标识
 */
const XFRAME = true;

/**
 * Xframe版本标识
 */
const XFRAME_VERSION = '0.0.1 beta';

/**
 * 应用入口地址
 */
define('INDEX_PATH', getcwd().'/');

/**
 * 框架地址
 */
define('X_PATH', __DIR__.'/');

/**
 * 框架内核地址
 */
define('XFRAME_PATH', X_PATH.'Xframe/');

/**
 * 引入必要文件
 */
require XFRAME_PATH.'Dispatcher.php';
require XFRAME_PATH.'LoaderInterface.php';
require XFRAME_PATH.'Loader.php';
require XFRAME_PATH.'BaseLoader.php';

/**
 * Xframe
 */
final class Xframe {
    
    static private $instance;
    private $dispatcher;
    
    static public function init(string $app_path = './'): self
    {
        if(!(self::$instance instanceof self))
        {
            self::$instance = new self($app_path);
        }
        self::$instance->setAppPath($app_path);
        return self::$instance;
    }

    private function __construct(string $appPath)
    {
        $this->dispatcher = Dispatcher::getInstance();
    }

    private function setAppPath(string $app_path): bool
    {
        $appRealPath = realpath($app_path);
        if(!$appRealPath)
        {
            throw new Exception('App path not exists');
        }
        define('APP_PATH', $appRealPath.'/');
        return true;
    }
    
    public function bootstrap(): self
    {
        try{
            if(!is_file(APP_PATH.'Bootstrap.php'))
            {
                throw new Expection('Have no Bootstrap.php in app dir');
            }
            require APP_PATH.'Bootstrap.php';
            $bootstrap = new Bootstrap();
            if(!($bootstrap instanceof BootstrapAbstract))
            {
                throw new Expection('Not extends BootstrapAbstract');
            }
            $reflect = new ReflectionClass($bootstrap);
            $methods = $reflect->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach($methods as $method)
            {
                $bootstrap->{$method->name}($this->dispatcher);
            }
            return $this;
        }
        catch(Expection $e)
        {
            exit('Expection');
        }
        catch(Error $e)
        {
            exit('Error');
        }
    }
    
    public function run(): bool
    {
        try
        {
            $router = $this->dispatcher->getRouter();
            $router->addRouter('default', RouteSimple::getInstance());
            $router->route();
            $controller = $this->dispatcher->getRequest()->getController();
            $params = $this->dispatcher->getRequest()->getParams();
            $class = '\\Controller\\'.$controller;
            if(!is_callable([$class, 'main']))
            {
                throw new Exception('Controller '.$controller.' is not exists');
            }
            $app = new $class($this->dispatcher);
            $app->construct();
            call_user_func_array(array($app, 'main'), $params);
            $app->destruct();
            return true;
        }
        catch(Expection $e)
        {
            echo $e->getMessage();
            return false;
        }
        catch(Error $e)
        {
            echo $e->getMessage();
            return false;
        }
    }
    
}
