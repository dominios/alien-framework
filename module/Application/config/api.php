<?php

return [
    'api' => [
        'route' => '/api',
        'namespace' => '',
        'action' => '',
        'childRoutes' => [
            'v1' => [
                'route' => '/v1',
                'namespace' => '',
                'action' => '',
                'childRoutes' => [
                    'navs' => [
                        'route' => '/navs[/:method][/:id]',
                        'namespace' => '',
                        'controller' => 'Application\Controllers\Rest\NavController',
                        'action' => 'rest',
                        'defaults' => [
                            'method' => 'list',
                        ]
                    ]
                ]
            ]
        ]
    ],
];