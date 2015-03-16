<?php

namespace Alien;

use Alien\Models\Authorization\Authorization;

ob_start();

try {
    require_once 'init.php';

    Application::boot();
//Authorization::getInstance();
    $application = Application::getInstance();
    echo $application->run();

} catch (\Exception $e) {
    echo "<h1>Internal Server Error</h1>";
    echo "<strong>" . get_class($e) . ": " . $e->getMessage() . "</strong> at <strong>" . $e->getFile() . "</strong> on line <strong>" . $e->getLine() . "</strong>";
    echo "<h2>Stack trace:</h2>";
    echo "<pre>";
    print_r($e->getTraceAsString());
    echo "</pre>";
}