<?php
namespace Xframe;
use Xframe\Dispatcher;

abstract class ControllerAbstract
{
    private $diapatcher;

    final public function __construct(Dispatcher $diapatcher)
    {
        $this->diapatcher = $diapatcher;
    }

    public function construct(){}

    abstract public function main();

    protected function getDispatcher()
    {
        return $this->diapatcher;
    }

    public function destruct(){}

    final public function __destruct()
    {

    }

}
