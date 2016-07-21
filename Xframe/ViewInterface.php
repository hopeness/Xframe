<?php
namespace Xframe;

interface ViewInterface
{

    public function render(string $tplPath = null, array $tplVars = null): string;
    
    public function display(string $tplPath = null, array $tplVars = null): bool;
    
    public function assign($name, $value = null): bool;
    
    public function setTplPath(string $dir): bool;

    public function getTplPath(): string;

}
