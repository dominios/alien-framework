<?php

use Alien\Application;
use Alien\Models\Authorization\UserDao;

require_once 'alien/init.php';

//Application::boot();
//$userDao = new UserDao(Application::getDatabaseHandler());
//echo '<pre>';
//$u = $userDao->find(1);
//print_r($u);
//$u->setSurname('Geršák');
//$userDao->update($u);

Application::boot();
$app = Application::getInstance();
$sm = $app->getServiceManager();
$userDao = $sm->getDao('Alien\Models\Authorization\UserDao');

echo "<pre>";
var_dump($userDao->find(1));
echo "</pre>";

