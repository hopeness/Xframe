<?php
namespace Xframe;
use Xframe\LoaderInterface;

final class BaseLoader implements LoaderInterface
{
    static private $instance;
    private $classPath;
    private $classFile;

    static public function getInstance(): self
    {
        if(!(self::$instance instanceof self)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function load(string $classPath): bool
    {
        $this->classPath = ltrim(str_replace('\\', '/', $classPath), '/');
        $prefix = substr($this->classPath, 0, strpos($this->classPath, '/'));
        switch($prefix)
        {
            case 'Xframe':
                $this->classFile = XFRAME_PATH.ltrim($this->classPath, $prefix.'/').'.php';
                break;
            case 'Controller':
                $this->classFile = APP_PATH.substr($this->classPath, 0, strlen($this->classPath) - 10).'.php';
                break;
            case 'Model':
                $this->classFile = APP_PATH.substr($this->classPath, 0, strlen($this->classPath) - 5).'.php';
                break;
            default:
                $this->classFile = APP_PATH.$this->classPath.'.php';
                break;
        }
        if(is_file($this->classFile))
        {
            require $this->classFile;
            return true;
        }
        return false;
    }

}
