<?php
namespace Xframe;
use Xframe\RequestAbstract;

interface RouteInterface {

    /**
     * route
     */
    public function route(RequestAbstract $request): bool ;

}

