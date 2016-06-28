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
        $pathInfo = '';
        if(isset($server['PATH_INFO']))
        {
            $pathInfo = $server['PATH_INFO'];
        }
        elseif(isset($server['REQUEST_URI']))
        {
            $pathInfo = $server['REQUEST_URI'];
        }
        else
        {

        }
        $pathInfo = trim($pathInfo, '/');
        $controller = empty($pathInfo) ? 'index' : $pathInfo;
        $ret = $request->setController($controller);
        return $ret;
    }

}
