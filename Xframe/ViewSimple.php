<?php

namespace Xframe;
use Xframe\ViewInterface;

class ViewSimple implements ViewInterface
{

    const TPL_EXT = '.phtml';

    private $basePath;
    private $tplFile;
    private $vars = array();

    public function __construct()
    {
        $this->basePath = APP_PATH.'View/';
        $this->tplFile = CONTROLLER.self::TPL_EXT;
    }

    public function render(string $tplPath = null, array $tplVars = null): string
    {
        ob_start();
        $this->display($tplPath, $tplVars);
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }
    
    public function display(string $tplPath = null, array $tplVars = null): bool
    {
        try
        {
            $tpl = $this->basePath.$this->tplFile;
            if(is_file($tpl))
            {
                extract($this->vars);
                require $tpl;
            }
            else
            {
                throw new Exception('Tpl file not extends');
            }
            return true;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
        finally
        {
            return false;
        }
    }
    
    public function assign($name, $value = null): bool
    {
        if(is_array($name))
        {
            array_merge($this->vars, $name);
        }else{
            $this->vars[$name] = $value;
        }
        return true;
    }
    
    public function setTplPath(string $path): bool
    {
        try
        {
            $path = realpath($path);
            if($path === false){
                throw new Exception('Path not extends');
            }
            $this->basePath = $path.'/';
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
        finally
        {
            return false;
        }
        
    }

    public function getTplPath(): string
    {
        return $this->basePath;
    }

}
