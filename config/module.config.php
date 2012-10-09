<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Authentication\Controller\Index' => 'Authentication\Controller\IndexController',
            'Authentication\Controller\Admin' => 'Authentication\Controller\AdminController',
        ),
    ),

    'controller_plugins' => array(
        'invokables' => array(
            'AuthenticationPlugin' => 'Authentication\Controller\Plugin\AuthenticationPlugin',
        ),
    ),

    'router' => array(
        'routes' => array(
            'login' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/auth/login',
                    'defaults' => array(
                        'controller' => 'Authentication\Controller\Index',
                        'action' => 'login',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/auth/logout',
                    'defaults' => array(
                        'controller' => 'Authentication\Controller\Index',
                        'action' => 'logout',
                    ),
                ),
            ),
            'authentication_admin_user' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/auth/user[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Authentication\Controller\Admin',
                        'action' => 'index',
                        'require_super_user' => true,
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'authentication' => __DIR__ . '/../views',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'identitty' => 'Authentication\View\Helper\Identity',
        )
    )
);
