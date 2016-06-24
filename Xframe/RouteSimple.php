<?php

namespace Xframe;
use Xframe\RouteInterface;

class RouteSimple implements RouteInterface
{

    static private $instance;

    static public function getInstance(): self
    {
        if(!(self::$instance instanceof self))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {

    }

    public function route(RequestAbstract $request): bool
    {
        $server = $request->getServer();
        $path = '';
        if(isset($server['PATH_INFO']))
        {
            $path = $server['PATH_INFO'];
        }
        elseif(isset($server['REQUEST_URI']))
        {
            $path = $server['REQUEST_URI'];
        }
        else
        {

        }
        $controller = trim($path, '/');
        $controller = empty($controller) ? 'index' : $controller;
        $request->setController($controller);
        return true;
    }

}
