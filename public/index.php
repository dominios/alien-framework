<?php

use Alien\Application;

chdir('..');

require_once 'alien/core/Application.php';
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
} catch (\Exception $e) {
    echo "<h1>Internal Server Error</h1>";
    echo "<strong>" . get_class($e) . ": " . $e->getMessage() . "</strong> at <strong>" . $e->getFile() . "</strong> on line <strong>" . $e->getLine() . "</strong>";
    echo "<h2>Stack trace:</h2>";
    echo "<pre>";
    print_r($e->getTraceAsString());
    echo "</pre>";
}