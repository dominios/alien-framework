<?php
require_once 'init.php';

if(!isset($_REQUEST['action'])){
    exit;
}

ob_start();

function displayLayoutType($REQ){    
    
    $type = $REQ['type'];
    
    $view = unserialize($_SESSION['SDATA']);    
    
    $view->DisplayLayout = $type;
    return $view->getContent();
}

function evalConsoleInput($REQ){
    $action = $REQ['data'];
    $ret = '';
    
    $ctrl = new ConsoleController(); 
    
    if(preg_match('/^php\s.*$/', $action)){
        error_reporting(0);
        $a = explode('php', $action, 2);
        $action = (string)$a[1];
        $ret .= (string)eval("".$action);
        $ret .= '<br>';
        echo $ret;
        exit;
    }
    
    if(method_exists($ctrl, $action) && !in_array($action, Array('init_action', 'getContent', '__construct', 'NOP', 'nop'))){
        
        $ret .= ('<span class="ConsoleTime">['.date('d.m.Y H:i:s', time()).']</span> <span class="'.AlienConsole::CONSOLE_MSG.'">'.$action.'<br>'.$ctrl->$action().'</span><br>');
    } else {
        $ret .= ('<span class="ConsoleTime">['.date('d.m.Y H:i:s', time()).']</span> <span class="'.AlienConsole::CONSOLE_ERROR.'">Command <i><b>'.$action.'</b></i> not recognized.</span><br>');
    }

    return $ret;
}

$action = $_REQUEST['action'];
$ret = $action($_REQUEST);
ob_clean();
echo $ret;

?>
