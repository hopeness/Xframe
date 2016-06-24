<?php
namespace Xframe;

abstract class ResponseAbstract
{

    static protected $instance;

    private $body;
    private $header;

    public static function getInstance(): self {
        if(!(self::$instance instanceof static)){
            self::$instance = new static();
        }
        return self::$instance;
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

    public function setRedirect(string $url) :bool {

    }
    
    public function __toString() :string {

    }

}
