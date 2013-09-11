<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace OAuth2Server;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use OAuth2\ZendHttpPhpEnvironmentBridge\Response;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        // This is to be able to load the models
        set_include_path(
            implode(
                PATH_SEPARATOR,
                array(
                    getenv('ADTRACKER_MODEL_PATH'),
                    getenv('ADTRACKER_MODEL_PATH') . '/generated',
                    get_include_path(),
                )
            )
        );

        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__),
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                // Override Response factory
                'Response' => function ($serviceManager) {
                    return new Response();
                },
                'OAuth2Server\Server' => function ($serviceManager) {
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
                'OAuth2Server\Storage' => function ($serviceManager) {
                    $config = $serviceManager->get('Config');
                    return new $config['oauth2server']['storage_class']();
                },
                'OAuth2Server\AuthorizeForm' => function ($serviceManager) {
                    $config = $serviceManager->get('Config');
                    $form = new $config['oauth2server']['authorize_form_class']('Authorize', $config['oauth2server']['csrf_salt']);
                    return $form;
                }
            )
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
}
