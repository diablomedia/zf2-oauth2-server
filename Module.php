<?php

namespace OAuth2Server;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use OAuth2\ZendHttpPhpEnvironmentBridge\Response;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__),
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return [
            'factories' => [
                // Override Response factory
                'Response' => function (ServiceManager $serviceManager) {
                    return new Response();
                },
                'OAuth2Server\Server' => function (ServiceManager $serviceManager) {
                    $config = $serviceManager->get('Config');
                    // The grant types and response types will automatically
                    // be determined based on the interfaces implemented in
                    // the storage_class
                    $server = new \OAuth2\Server(
                        $serviceManager->get('OAuth2Server\Storage'),
                        $config['oauth2server']['server_config']
                    );

                    return $server;
                },
                'OAuth2Server\Storage' => function (ServiceManager $serviceManager) {
                    $config = $serviceManager->get('Config');

                    return new $config['oauth2server']['storage_class']();
                },
                'OAuth2Server\AuthorizeForm' => function (ServiceManager $serviceManager) {
                    $config = $serviceManager->get('Config');
                    $form = new $config['oauth2server']['authorize_form_class'](
                        'Authorize',
                        $config['oauth2server']['csrf_salt']
                    );

                    return $form;
                }
            ]
        ];
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return void
     */
    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
}
