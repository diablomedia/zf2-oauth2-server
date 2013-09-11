<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'OAuth2Server\Controller\Authorize' => 'OAuth2Server\Controller\AuthorizeController'
        )
    ),
    'router' => array(
        'routes' => array(
            'oauth_auth' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/oauth',
                    'defaults' => array(
                        '__NAMESPACE__' => 'OAuth2Server\Controller',
                        'controller'    => 'Authorize',
                        'action'        => 'authorize'
                    )
                ),
                'may_terminate' => true,
            ),
            'oauth_token' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/oauth/token',
                    'defaults' => array(
                        '__NAMESPACE__' => 'OAuth2Server\Controller',
                        'controller'    => 'Authorize',
                        'action'        => 'token'
                    )
                ),
                'may_terminate' => true,
            )
        )
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy'
        ),
        'template_map' => array(
            'o-auth2-server/authorize/authorize' => __DIR__ . '/../view/authorize.phtml'
        ),
    ),
    'oauth2server' => array(
        'storage_class' => '\OAuth2\Storage\Memory',
        'authorize_form_class' => '\OAuth2Server\Form\AuthorizeForm',
        'server_config' => array(
            //'access_lifetime'          => 3600,
            //'www_realm'                => 'Service',
            //'token_param_name'         => 'access_token',
            //'token_bearer_header_name' => 'Bearer',
            //'enforce_state'            => true,
            //'require_exact_redirect_uri' => true,
            //'allow_implicit'           => false,
            //'allow_credentials_in_request_body' => true,
        ),
    ),
);
