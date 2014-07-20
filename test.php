<?php

use Alien\Application;
use Alien\Models\Authorization\UserDao;
use Alien\Annotaion\Auth;

require_once 'alien/init.php';

Application::boot();
$app = Application::getInstance();
$sm = $app->getServiceManager();

//$testRoute = 'http://alien.localhost/alien/dashboard/home';
$testRoute = 'http://alien.localhost/alien/users/edit/id/1/admin/0';
//$testRoute = 'http://alien.localhost/kontakt';

//$ar = new \Alien\Annotaion\AnnotationEngine();
//
//class test {
//
//    /**
//     * @Alien\Annotaion\Auth
//     */
//    public function echoA() {
//        echo 'A';
//    }
//}
//
//$c = new test();
//
//$x = $ar->getMethodAnnotaions($c, 'echoA');
//

echo "<pre>";
$rt = new \Alien\Router();
$rt->handle($testRoute);
echo "</pre>";

