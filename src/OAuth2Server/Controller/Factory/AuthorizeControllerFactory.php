<?php

namespace OAuth2Server\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use OAuth2Server\Controller\AuthorizeController;
use Psr\Container\ContainerInterface;

class AuthorizeControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AuthorizeController(
            $container->get('OAuth2Server\Server'),
            $container->get('OAuth2Server\AuthorizeForm')
        );
    }
}
