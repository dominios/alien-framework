<?php

return array(
    'dashboard' => array(
        'route' => '/dashboard',
        'controller' => 'DashboardController',
        'namespace' => 'Alien\Controllers',
        'action' => 'home'
    ),
    'user' => array(
        'route' => '/user',
        'controller' => 'UsersController',
        'namespace' => 'Alien\Controllers',
        'childRoutes' => array(
            'list' => array(
                'route' => '/list',
                'action' => 'viewList',
            ),
            'edit' => array(
                'route' => '/edit/:id',
                'action' => 'edit'
            ),
            'remove' => array(
                'route' => '/remove/:id',
                'action' => 'remove'
            )
        )
    )
);

//return array(
//    'routes' => array(
//        'kontakt' => array(
//            'controller' => 'FrontController',
//            'namespace' => 'Alien\Controllers',
//            'action' => 'kontakt',
//            'query' => false,
//        ),
//        'default' => array(
//            'prefix' => 'alien',
//            'pattern' => '{controller}/{action}/{query}',
//            'controller' => '{name}Controller',
//            'namespace' => 'Alien\Controllers',
//            'action' => '{name}',
//            'query' => false
//        )
//    )
//);