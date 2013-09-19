<?php

namespace Alien\Controllers;

ob_start();
require_once 'init.php';

if (!isset($_REQUEST['action'])) {
    exit;
} else {

    $action = $_REQUEST['action'];

    $Controller = new AjaxController();
    $response = $Controller->$action($_REQUEST);
    $data = $response->getData();

    ob_clean();
    echo $data['result'];
}

