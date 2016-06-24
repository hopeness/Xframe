<?php
namespace Xframe;
use Xframe\BaseLoader;

final class Loader
{
    static private $instance;

    static public function getInstance(): self
    {
        if(!(self::$instance instanceof self)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->setLoader(BaseLoader::getInstance());
    }
    
    public function setLoader(LoaderInterface $loader): bool
    {
        return spl_autoload_register([$loader, 'load']);
    }

}
