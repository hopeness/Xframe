<?php
namespace Xframe;

abstract class RequestAbstract
{

    static protected $instance;
    private $get = null;
    private $post = null;
    private $cookie = null;
    private $files = null;
    private $server = null;
    private $env = null;
    private $method = null;
    private $language = null;
    private $controller = 'index';
    private $params = [];
    private $routed = false;

    private function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->cookie = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->env = $_ENV;
        $this->method = $this->SERVER['method'];
    }

    final public static function getInstance(): self
    {
        if(!(self::$instance instanceof static)){
            self::$instance = new static();
        }
        return self::$instance;
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
