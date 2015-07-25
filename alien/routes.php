<?php

return array(
    'login' => array(
        'route' => '/login',
        'controller' => 'BaseController',
        'namespace' => 'Alien\Controllers',
        'action' => 'loginScreen'
    ),
    'logout' => array(
        'route' => '/logout',
        'controller' => 'BaseController',
        'namespace' => 'Alien\Controllers',
        'action' => 'logout'
    ),
    'dashboard' => array(
        'route' => '/dashboard',
        'controller' => 'DashboardController',
        'namespace' => 'Alien\Controllers',
        'action' => 'home'
    ),
);
