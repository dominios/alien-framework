<?php

return [
    'api' => [
        'route' => '/api',
        'namespace' => 'Application\Controllers',
        'action' => 'index',
        'childRoutes' => [
            'v1' => [
                'route' => '/v1',
                'childRoutes' => [
                    'nav' => [
                        'route' => '/nav/:method[/:id]',
                        'controller' => 'Application\Controllers\Rest\NavController',
                        'action' => 'indexAction'
                    ]
                ]
            ]
        ]
    ],
];