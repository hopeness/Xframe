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
        self::$instance->SERVER = $_SERVER;
        self::$instance->REQUEST = $_REQUEST;
        self::$instance->ENV = $_ENV;
        self::$instance->QUERY = $_GET;
        self::$instance->POST = $_POST;
        self::$instance->FILES = $_FILES;
        self::$instance->COOKIE = $_COOKIE;
        return self::$instance;
    }

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
        if($this->routed)
        {
            return false;
        }
        if(empty($controller)){
            throw new Exception('Controller is empty');
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
        if($this->routed)
        {
            return false;
        }
        $this->params = $params;
        return true;
    }

    final public function routed(): bool
    {
        $this->routed = true;
        return true;
    }

}
