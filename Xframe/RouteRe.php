<?php

namespace Xframe;
use Xframe\RouteInterface,
    Xframe\RequestAbstract;

class RouteRe implements RouteInterface
{

    private $conf;

    public function __construct(array $conf)
    {
        $this->conf = $conf;
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
        $controller = '';
        foreach($this->conf as $pattern => $ctl)
        {
            if(preg_match('#'.$pattern.'#', $pathInfo, $matches))
            {
                $controller = $ctl;
                array_shift($matches);
                $request->setParams($matches);
            }
        }

        $ret = $request->setController($controller);
        return $ret;
    }

}
