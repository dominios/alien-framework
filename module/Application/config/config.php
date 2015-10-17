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

    'services' => [
        'Router' => function (\Alien\Di\ServiceLocator $sl) {
            $routes = include 'routes.php';
            $api = include 'api.php';
            return new \Alien\Routing\Router(array_merge($routes, $api));
        },
        'Connection' => function (\Alien\Di\ServiceLocator $sl) {
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
        'Authorization' => function (\Alien\Di\ServiceLocator $sl) {
            $auth = new \Alien\Rbac\Authorization($sl);
            return $auth;
        },
    ]

];