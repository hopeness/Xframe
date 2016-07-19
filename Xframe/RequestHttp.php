<?php
namespace Xframe;
use Xframe\RequestAbstract;

final class RequestHttp extends RequestAbstract
{

    private $method;
    private $language;

    protected function construct()
    {
        $this->method = $this->SERVER['REQUEST_METHOD'];
    }

    public function getPostRaw()
    {
        return file_get_contents('php://input');
    }

    public function getMethod() :string
    {
        return $this->method;
    }

    public function getLanguage() :string
    {
        return '';
    }

}
