<?php
use Psr\Container\ContainerInterface;

class ContainerService
{
    private static $oContainer = null;

    private function __construct()
    {
    }

    public static function getInstance() : ContainerInterface
    {
        if (self::$oContainer === null) {
            $oBuilder = new \DI\ContainerBuilder();
            $oBuilder->addDefinitions(require_once dirname(__FILE__)."/../config/config.di.php");
            self::$oContainer = $oBuilder->build();
        }

        return self::$oContainer;
    }
}
