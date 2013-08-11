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

//$content = str_replace('</body></html>', '', $content);
//$content .= '<div style="position: absolute; top: 0px; width: 100%;"><div id="notifyArea" style="display: block;"></div></div>';
//$content .= Notification::renderNotifications();
//$content .= '<script type="text/javascript"> $(document).ready(function(){ showNotifications(); }); </script>';
//$content .= '</body></html>';
//
// output
//
ob_end_clean();
echo $content;

