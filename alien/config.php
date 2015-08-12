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
        'Router' => function(\Alien\Di\ServiceLocator $sl) {
            $routes = include 'routes.php';
            return new \Alien\Routing\Router($routes);
        },
        'Connection' => function(\Alien\Di\ServiceLocator $sl) {
            $conf = $sl->getService('\Alien\Configuration')->get('database');
            $connection = new Alien\Db\Connection(
                $conf['host'],
                $conf['user'],
                $conf['password'],
                $conf['database']
            );
            $connection->setDbPrefix($conf['prefix']);
            return $connection;
        },
        'UserDao' => function(\Alien\Di\ServiceLocator $sl) {
            $userDao = new \Alien\Models\Authorization\UserDao($sl->getService('PDO'), $sl);
            return $userDao;
        }
    ]

];