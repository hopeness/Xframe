<?php
namespace Xframe;
use Xframe\RequestAbstract;

interface RouteInterface
{

    /**
     * route
     * @param RequestAbstract $request
     * @return bool
     */
    public function route(RequestAbstract $request): bool;

}

