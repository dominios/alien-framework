<?php

use Alien\Application;

chdir('..');

require_once 'alien/init.php';


// test routingu

$urls = [
    "route",
    "route/value1",
    "route/sub",
    "route/sub/p1/p2"
];

try {
    Application::boot();
    $app = Application::getInstance();
    $app->run();
} catch (Exception $e) {
    echo '<pre>';
    print_r($e);
}