<?php
namespace Core;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return [
            'factories' => [
                __NAMESPACE__ . '\Mvc\Controller\AbstractActionController' => Mvc\Controller\AbstractActionController::class,
            ],
        ];
    }

    public function getAutoloaderConfig()
    {
        return array(
            // 'Zend\Loader\ClassMapAutoloader' => array(
            //     __DIR__ . '/autoload_classmap.php',
            // ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ ."/". str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }
}