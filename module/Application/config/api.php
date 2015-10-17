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
                    'nav' => [
                        'route' => '/nav/:method[/:id]',
                        'namespace' => '',
                        'controller' => 'Application\Controllers\Rest\NavController',
                        'action' => 'index'
                    ]
                ]
            ]
        ]
    ],
];