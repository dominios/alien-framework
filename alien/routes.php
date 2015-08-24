<?php

return array(
    'route' => array(
        'route' => '/route',
        'controller' => 'AbstractController',
        'namespace' => 'Alien\Controllers',
        'action' => 'index',
        'childRoutes' => [
            'sub' => [
                'route' => '/sub',
                'controller' => 'SubController',
                'action' => 'some'
            ]
        ]
    ),
);
