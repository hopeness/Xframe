<?php
namespace Xframe;
use Xframe\RequestAbstract;

final class RequestCli extends RequestAbstract {

    private $get;
    private $post;
    private $cookie;
    private $files;
    private $server;
    private $env;
    private $method;
    private $language;

    protected function __construct(){
        $this->get = $_GET;
        $this->post = $_POST;
        $this->cookie = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->env = $_ENV;
        $this->method = strtoupper($this->server['REQUEST_METHOD']);
    }

    public function getQuery() :array {
        return $this->get;
    }

    public function getPost() :array {
        return $this->post;
    }
    
    public function getRequest() :array {
        return array_merge($this->get, $this->post);
    }

    public function getRaw(){
        return file_get_contents('php://input');
    }

    public function getFiles() :array {
        return $this->files;
    }

    public function getServer() :array {
        return $this->server;
    }

    public function getEnv() :array {
        return $this->env;
    }

    public function getMethod() :string {
        return $this->method;
    }

    public function getLanguage() :string {
        return '';
    }

}
