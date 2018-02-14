<?php

return [
    'controllers' => [
        'factories' => [
            'OAuth2Server\Controller\Authorize' => OAuth2Server\Controller\Factory\AuthorizeControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'oauth_auth' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/oauth',
                    'defaults' => [
                        '__NAMESPACE__' => 'OAuth2Server\Controller',
                        'controller'    => 'Authorize',
                        'action'        => 'authorize'
                    ]
                ],
                'may_terminate' => true,
            ],
            'oauth_token' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/oauth/token',
                    'defaults' => [
                        '__NAMESPACE__' => 'OAuth2Server\Controller',
                        'controller'    => 'Authorize',
                        'action'        => 'token'
                    ]
                ],
                'may_terminate' => true,
            ]
        ]
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy'
        ],
        'template_map' => [
            'o-auth2-server/authorize/authorize' => __DIR__ . '/../view/authorize.phtml'
        ],
    ],
    'oauth2server' => [
        'storage_class'        => '\OAuth2\Storage\Memory',
        'authorize_form_class' => '\OAuth2Server\Form\AuthorizeForm',
        'server_config'        => [],
    ],
];
