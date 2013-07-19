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
if(sizeof($_GET)){
    $ctrl = ucfirst(strtolower(key($_GET))).'Controller';
    try {
        $controller = new $ctrl;
    } catch(Exception $ex){
        Alien::getInstance()->getConsole()->putMessage('Called controller <i>'.$ctrl.'</i> doesn\'t exist!', AlienConsole::CONSOLE_ERROR);
        $controller = new AlienController;
    }
} else {
    $controller = new AlienController;
}
$content .= $controller->getContent();

//
// output
//
ob_end_clean();
echo $content;
?>
