<?php

use Alien\Application;
use Alien\Models\Authorization\UserDao;
use Alien\Annotaion\Auth;

require_once 'alien/init.php';

Application::boot();
$app = Application::getInstance();
$sm = $app->getServiceManager();


$testedRoutes = array(
    '/kontakt',
    '/user',
    '/user/edit/1',
    //    '/user/edit', // spravne uz dava exception
    //    '/blbost', // spravne dava exception
    //    '/user/delete/3', // spravne dava exception
    '/user/remove/3',
    //    '/user/remove/', // spravne dava exception
);


echo "<pre>";

$router = new \Alien\Router();

foreach ($testedRoutes as $r) {
    $router->getMatch($r);
}


echo "</pre>";

