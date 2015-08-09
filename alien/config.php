<?php

return [

    'timezone' => 'Europe/Bratislava',

    'database' => [
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => '',
        'database' => 'test',
        'prefix' => 'test'
    ],

    'factories' => [
        'Router' => function(\Alien\Di\ServiceLocator $sm) {
            $routes = include 'routes.php';
            return new \Alien\Routing\Router($routes);
        },
        'UserDao' => function(\Alien\Di\ServiceLocator $sm) {
            $userDao = new \Alien\Models\Authorization\UserDao($sm->getService('PDO'), $sm);
            return $userDao;
        }
    ]

];