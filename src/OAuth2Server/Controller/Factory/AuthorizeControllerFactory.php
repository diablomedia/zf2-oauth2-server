<?php

namespace OAuth2Server\Controller\Factory;

use Zend\ServiceManager\FactoryInterface;
use OAuth2Server\Controller\AuthorizeController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthorizeControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), AuthorizeController::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AuthorizeController(
            $container->get('OAuth2Server\Server'),
            $container->get('OAuth2Server\AuthorizeForm')
        );
    }
}
