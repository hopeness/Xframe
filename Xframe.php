<?php
/**
 * Xframe
 * @author     Peter <houpengg@gmail.com>
 * @link       https://github.com/hopeness/Xframe
 * @license    https://github.com/hopeness/Xframe/blob/master/LICENSE GNU License
 */

/**
 * strict type
 */
declare(strict_types=1);

use Xframe\Dispatcher,
    Xframe\BootstrapAbstract;

/**
 * Xframe sysbol
 */
const XFRAME = true;

/**
 * Xframe version
 */
const XFRAME_VERSION = '0.0.1 beta';

/**
 * App enter
 */
define('INDEX_PATH', getcwd().'/');

/**
 * Define path of xframe
 */
define('X_PATH', __DIR__.'/');

/**
 * Define path of core
 */
define('XFRAME_PATH', X_PATH.'Xframe/');

/**
 * Require base file
 */
require XFRAME_PATH.'Dispatcher.php';
require XFRAME_PATH.'LoaderInterface.php';
require XFRAME_PATH.'Loader.php';
require XFRAME_PATH.'BaseLoader.php';

/**
 * Xframe class
 */
final class Xframe {
    
    /**
     * Core instance
     */
    static private $instance;

    /**
     * Dispatcher instance
     */
    private $dispatcher;
    

    /**
     * init
     * @param string $appPath app path, default "./"
     * @return Xframe
     */
    static public function init(string $appPath = './'): self
    {
        if(!(self::$instance instanceof self))
        {
            self::$instance = new self($appPath);
        }
        self::$instance->setAppPath($appPath);
        return self::$instance;
    }

    /**
     * private __construct
     * @param string $appPath  app path
     */
    private function __construct(string $appPath)
    {
        $this->dispatcher = Dispatcher::getInstance();
    }

    /**
     * public bootstrap
     * @return Xframe
     */
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
            // Use reflection to find public function of bootstrap
            $reflect = new ReflectionClass($bootstrap);
            $methods = $reflect->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach($methods as $method)
            {
                // Execute every public function
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
    
    /**
     * public run
     * @return Xframe
     */
    public function run(): bool
    {
        try
        {
            // Route
            $router = $this->dispatcher->getRouter();
            $routeStatus = $router->route();
            if($routeStatus === false)
            {
                throw new Exception('Route failed');
            }
            // Get controller
            $controller = $this->dispatcher->getRequest()->getController();
            // Get params
            $params = $this->dispatcher->getRequest()->getParams();
            // Make namespace of controller
            $class = '\\Controller\\'.$controller;
            if(!is_callable([$class, 'main']))
            {
                throw new Exception('Controller '.$controller.' is not exists');
            }
            $app = new $class($this->dispatcher);
            $app->construct();
            // Call controller
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

    /**
     * private set app path
     * @param string $appPath app path
     * @return bool
     */
    private function setAppPath(string $appPath): bool
    {
        $appRealPath = realpath($appPath);
        if(!$appRealPath)
        {
            throw new Exception('App path not exists');
        }
        define('APP_PATH', $appRealPath.'/');
        return true;
    }
    
}
