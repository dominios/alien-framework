<?php

namespace Alien;

use Alien\Controllers\BaseController;

//
// init
//
ob_start();
$content = '';
require_once 'init.php';
Alien::getInstance();

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
    Alien::getInstance()->getConsole()->putMessage('Called controller <i>' . $controller . '</i> doesn\'t exist!', Terminal::ERROR);
    $controller = new BaseController($actionsArray);
}

//if(sizeof($_GET)){
//    $ctrl = ucfirst(strtolower(key($_GET))).'Controller';
//    try {
//        $controller = new $ctrl;
//    } catch(Exception $ex){
//        Alien::getInstance()->getConsole()->putMessage('Called controller <i>'.$ctrl.'</i> doesn\'t exist!', AlienConsole::CONSOLE_ERROR);
//        $controller = new AlienController;
//    }
//} else {
//    $controller = new AlienController();
//}

$content .= $controller->renderToString();

//$content = str_replace('</body></html>', '', $content);
//$content .= '<div style="position: absolute; top: 0px; width: 100%;"><div id="notifyArea" style="display: block;"></div></div>';
//$content .= Notification::renderNotifications();
//$content .= '<script type="text/javascript"> $(document).ready(function(){ showNotifications(); }); </script>';
//$content .= '</body></html>';
//
// output
//
ob_end_clean();
header('Content-Type: text/html; charset=utf-8');
echo $content;