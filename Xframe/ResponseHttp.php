<?php
namespace Xframe;
use Xframe\ResponseAbstract;

final class ResponseHttp extends ResponseAbstract {

    private $body;
    private $header;

    protected function __construct(){

    }

    public function setBody(string $body, string $name = NULL) :bool {

    }

    public function prependBody(string $body, string $name = NULL) :bool {

    }

    public function appendBody(string $body, string $name = NULL) :bool {

    }

    public function clearBody() :bool {

    }

    public function getBody() :string {

    }

    public function response() :bool {

    }

    public function redirect(string $url) :bool {

    }

    public function __toString() :string {

    }

}
