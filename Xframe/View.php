<?php

namespace Xframe;

use Xframe\ViewInterface,
    Xframe\ViewSimple;

final class View
{
    static private $instance;

    private $views = [];
    private $viewPoint;

    static public function getInstance(): self
    {
        if(!(self::$instance instanceof self))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addView($name, ViewInterface $view): bool
    {
        $this->views[$name] = $view;
        return true;
    }

    public function selectView($name): bool
    {
        if(isset($this->views[$name]))
        {
            $this->viewPoint = &$this->views[$name];
            return true;
        }
        return false;
    }

    public function setDefault(): bool
    {
        if(empty($this->views))
        {
            $this->views['default'] = new ViewSimple();
        }
        $this->viewsPoint = $this->views[key($this->views)];
        return true;
    }

    public function getView(): ViewInterface
    {
        return $this->viewsPoint;
    }

}
