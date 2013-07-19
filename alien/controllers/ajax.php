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


$action = $_REQUEST['action'];
$ret = $action($_REQUEST);
ob_clean();
echo $ret;

?>
