<?php
/**
 * Frame dispatcher
 * @author     Peter <houpengg@gmail.com>
 * @link       https://github.com/hopeness/Xframe
 * @license    https://github.com/hopeness/Xframe/blob/master/LICENSE GNU License
 */

namespace Xframe;
use Xframe\Loader,
    Xframe\RequestAbstract,
    Xframe\RequestHttp,
    Xframe\RequestCli,
    Xframe\ResponseAbstract,
    Xframe\ResponseHttp,
    Xframe\ResponseCli,
    Xframe\Router,
    Xframe\View;

final class Dispatcher
{

    static private $instance;
    private $loaderInstance;
    private $requestInstance;
    private $responseInstance;
    private $routerInstance;
    private $viewInstance;

    static public function getInstance(): self
    {
        if(!(self::$instance instanceof self))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->loaderInstance = Loader::getInstance();
        if(PHP_SAPI === 'cli')
        {
            $this->requestInstance = RequestCli::getInstance();
            $this->responseInstance = ResponseCli::getInstance();
        }
        else
        {
            $this->requestInstance = RequestHttp::getInstance();
            $this->responseInstance = ResponseHttp::getInstance();
        }
        $this->routerInstance = Router::getInstance($this->requestInstance);
        $this->viewInstance = View::getInstance($this->requestInstance);
    }

    public function getLoader(): Loader
    {
        return $this->loaderInstance;
    }

    public function getRequest(): RequestAbstract
    {
        return $this->requestInstance;
    }

    public function getRouter(): Router
    {
        return $this->routerInstance;
    }

    public function getView(): View
    {
        return $this->viewInstance;
    }

    public function getResponse(): ResponseAbstract
    {
        return $this->responseInstance;
    }

    public function __destruct(){

    }

}
