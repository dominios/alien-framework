<?php

namespace Alien;

use Alien\Controllers\BaseController;

//
// init
//
ob_start();
$content = '';
require_once 'init.php';
Application::boot();

//
// work
//
$actionsArray = array();
# najprv POST
if (@sizeof($_POST)) {
    $arr = explode('/', $_POST['action'], 2);
    $controller = $arr[0];
    $actionsArray[] = $arr[1];
}

$request = str_replace('/alien', '', $_SERVER['REQUEST_URI']);
$keys = explode('/', $request, 4);
// zacina sa / takze na indexe 0 je prazdny string
// 1 - controller
// 2 - akcia
// 3 - zatial zvysok parametre (GET)
if (empty($controller)) {
    $controller = $keys[1];
}
if ($keys[2] !== null) {
    $actionsArray[] = $keys[2];
}
$params = explode('/', preg_replace('/\?.*/', '', $keys[3])); // vyhodi vsetko ?... cize "stary get"

if (count($params) >= 2) {
    unset($_GET);
    for ($i = 0; $i < count($params); $i++) {
        $_GET[$params[$i++]] = $params[$i];
    }
} else {
    unset($_GET);
    $_GET['id'] = $params[0];
}


$controller = __NAMESPACE__ . '\Controllers\\' . ucfirst($controller) . 'Controller';

if (class_exists($controller)) {
    $controller = new $controller($actionsArray);
} else {
//    Application::getInstance()->getConsole()->putMessage('Called controller <i>' . $controller . '</i> doesn\'t exist!', Terminal::ERROR);
    $controller = new BaseController($actionsArray);
}

$content .= $controller->renderToString();

//
// output
//
ob_end_clean();
header('Content-Type: text/html; charset=utf-8');
echo $content;