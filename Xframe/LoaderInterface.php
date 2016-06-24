<?php
namespace Xframe;

interface LoaderInterface
{

    public function load(string $classPath) :bool;

}
