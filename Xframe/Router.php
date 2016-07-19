<?php

namespace Xframe;
use Xframe\RequestAbstract,
    Xframe\Routernterface,
    Xframe\RouteSimple;

final class Router
{

    static private $instance;

    private $request;
    private $controller;
    private $routers = [];

    static public function getInstance(RequestAbstract $request): self
    {
        if(!(self::$instance instanceof self))
        {
            self::$instance = new self($request);
        }
        return self::$instance;
    }

    private function __construct(RequestAbstract $request)
    {
        $this->request = $request;
    }

    public function addRouter($name, RouteInterface $router): bool
    {
        $this->routers[$name] = $router;
        return true;
    }

    public function delRouter($name)
    {
        unset($this->routers[$name]);
    }

    public function route(): bool
    {
        if(empty($this->routers)){
            $this->routers['default'] = new RouteSimple();
        }
        $routeRet = false;
        foreach($this->routers as $router){
            $ret = $router->route($this->request);
            if($ret){
                $routeRet = true;
                break;
            }
        }
        $this->request->routed();
        return $routeRet;
    }

}
