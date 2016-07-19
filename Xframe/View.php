<?php

namespace Xframe;

use Xframe\ViewInterface;

final class View
{
    static private $instance;

    private $views = [];
    private $view;

    static public function getInstance(): self
    {
        if(!(self::$instance instanceof self))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {

    }

    public function setView($name, ViewInterface $view): bool
    {
        $this->views[$name] = $view;
        return true;
    }

    public function routed($name, ViewInterface $view): bool
    {
        $this->views[$name] = $view;
        return true;
    }

    public function setTpl($name): bool
    {
        if(isset($views[$name]))
        {
            $view = $views[$name];
            return true;
        }
        else
        {
            return false;
        }
        return $this->diapatcher->getView()->setTpl($name);
    }

    public function render(string $tplPath = null, array $tplVars = null): string
    {
        return $this->diapatcher->getView()->render($tplPath, $tplVars);
    }

    public function display(string $tplPath = null, array $tplVars = null): bool
    {
        return $this->diapatcher->getView()->display($tplPath, $tplVars);
    }

    public function assign(mixed $name, mixed $value): bool
    {
        return $this->diapatcher->getView()->assign($name, $value);
    }

}
