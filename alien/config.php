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
        'Router' => function(\Alien\Di\ServiceManager $sm) {
            return new \Alien\Router();
        },
        'UserDao' => function(\Alien\Di\ServiceManager $sm) {
            $userDao = new \Alien\Models\Authorization\UserDao($sm->getService('PDO'), $sm);
            return $userDao;
        }
    ]

];