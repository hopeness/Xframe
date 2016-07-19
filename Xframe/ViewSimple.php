<?php

namespace Xframe;
use Xframe\ViewInterface;

class ViewSimple implements ViewInterface
{

    const TPL_EXT = '.tpl.php';

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
    
    public function assign($name, $value): bool
    {
        $this->vars[$name] = $value;
        return true;
    }
    
    public function setTplPath(string $dir): bool
    {
        return true;
    }

    public function getTplPath(): string
    {

    }

}
