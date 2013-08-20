<?php
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
//$request = str_replace('/alien', '', $_SERVER['REQUEST_URI']);
//$keys = explode('/', $request, 4);
//// zacina sa / takze na indexe 0 je prazdny string
//// 1 - controller
//// 2 - akcia
//// 3 - zatial zvysok parametre (GET)
//$controller = $keys[1];
//$action = $keys[2];
//$params = explode('/',$keys[3]);
//
//if(count($params) >= 2){
//    for($i=0; $i < count($params); $i++){
//        $_GET[$params[$i++]] = $params[$i];
//    }
//} else {
//    $_GET = $params;
//}
//
//$controller = ucfirst($controller).'Controller';
//try {
//    $controller = new $controller($action);
//} catch(Exception $e){
//    Alien::getInstance()->getConsole()->putMessage('Called controller <i>'.$ctrl.'</i> doesn\'t exist!', AlienConsole::CONSOLE_ERROR);
//    $controller = new AlienController($action);
//}

if(sizeof($_GET)){
    $ctrl = ucfirst(strtolower(key($_GET))).'Controller';
    try {
        $controller = new $ctrl;
    } catch(Exception $ex){
        Alien::getInstance()->getConsole()->putMessage('Called controller <i>'.$ctrl.'</i> doesn\'t exist!', AlienConsole::CONSOLE_ERROR);
        $controller = new AlienController;
    }
} else {
    $controller = new AlienController();
}

$content .= $controller->getContent();

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

