<?php
namespace Xframe;

abstract class RequestAbstract
{

    static protected $instance;

    protected $SERVER;
    protected $QEQUEST;
    protected $ENV;
    protected $GET;
    protected $POST;
    protected $FILES;
    protected $COOKIE;

    protected $routed = false;
    protected $controller = '';
    protected $params = [];


    final public static function getInstance(): self
    {
        if(!(self::$instance instanceof self)){
            self::$instance = new static();
        }
        self::$instance->construct();
        return self::$instance;
    }

    final private function __construct()
    {
        $this->SERVER = $_SERVER;
        $this->REQUEST = $_REQUEST;
        $this->ENV = $_ENV;
        $this->GET = $_GET;
        $this->POST = $_POST;
        $this->FILES = $_FILES;
        $this->COOKIE = $_COOKIE;
    }

    abstract protected function construct();

    final public function getServer(): array
    {
        return $this->SERVER;
    }

    final public function getRequest(): array
    {
        return $this->REQUEST;
    }

    final public function getEnv(): array
    {
        return $this->ENV;
    }

    final public function getQuery(): array
    {
        return $this->GET;
    }

    final public function getPost(): array
    {
        return $this->POST;
    }

    final public function getFiles(): array
    {
        return $this->FILES;
    }

    final public function getCookie(): array
    {
        return $this->COOKIE;
    }

    final public function getSession(): array
    {
        return $_SESSION;
    }

    final public function getController(): string
    {
        return $this->controller;
    }

    final public function setController(string $controller): bool
    {
        if(empty($controller))
        {
            return false;
        }
        $this->controller = strtolower($controller);
        return true;
    }

    final public function getParams(): array
    {
        return $this->params;
    }

    final public function setParams(array $params): bool
    {
        $this->params = $params;
        return true;
    }

}
