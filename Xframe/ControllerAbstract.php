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

    final protected function getDispatcher(): Dispatcher
    {
        return $this->diapatcher;
    }

    final protected function setTpl($name): bool
    {
        return $this->diapatcher->getView()->setTpl($name);
    }

    final protected function render(string $tplPath = null, array $tplVars = null): string
    {
        return $this->diapatcher->getView()->render($tplPath, $tplVars);
    }

    final protected function display(string $tplPath = null, array $tplVars = null): bool
    {
        return $this->diapatcher->getView()->display($tplPath, $tplVars);
    }

    final protected function assign(mixed $name, mixed $value): bool
    {
        return $this->diapatcher->getView()->assign($name, $value);
    }

    public function destruct(){}

    final public function __destruct()
    {

    }

}
