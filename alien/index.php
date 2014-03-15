<?php

namespace Alien;

use Alien\Controllers\BaseController;

ob_start();
$content = '';
require_once 'init.php';
Application::boot();

$request = BaseController::parseRequest();

if (class_exists($request['controller'])) {
    $controller = new $request['controller']($request['actions']);
} else {
//    Application::getInstance()->getConsole()->putMessage('Called controller <i>' . $controller . '</i> doesn\'t exist!', Terminal::ERROR);
    $controller = new BaseController($request['actions']);
}

$content .= $controller->renderToString();

ob_end_clean();
header('Content-Type: text/html; charset=utf-8');
echo $content;