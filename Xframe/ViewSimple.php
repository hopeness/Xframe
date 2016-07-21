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
    }

    public function render(string $tplPath = null, array $tplVars = null): string
    {
        ob_start();
        $status = $this->display($tplPath, $tplVars);
        $data = ob_get_contents();
        ob_end_clean();
        if($status === false)
        {
            echo $data;
            return '';
        }
        return $data;
    }
    
    public function display(string $tplPath = null, array $tplVars = null): bool
    {
        try
        {
            if($tplPath === null)
            {
                $tplPath = CONTROLLER;
            }
            if(is_array($tplVars)){
                $this->assign($tplVars);
            }
            $tpl = $this->basePath.$tplPath.self::TPL_EXT;
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

    public function getTplPath(): string
    {
        return $this->basePath;
    }

}
