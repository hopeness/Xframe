<?php

namespace Xframe;
use Xframe\ViewInterface;

class ViewSimple implements ViewInterface
{

    public function render(string $tplPath = null, array $tplVars = null): string
    {

    }
    
    public function display(string $tplPath = null, array $tplVars = null): bool
    {
        return true;
    }
    
    public function assign($name, $value): bool
    {
        var_dump($name);
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
