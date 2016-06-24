<?php
namespace Xframe\Api;

interface ViewInterface {

    public function render(string $view_path, array $tpl_vars = NULL) :string;
    
    public function display(string $view_path, array $tpl_vars = NULL) :bool;
    
    public function assign(mixed $name, mixed $value = NULL): bool;
    
    public function setScriptPath(string $view_directory): bool;

    public function getScriptPath() :string;

}
