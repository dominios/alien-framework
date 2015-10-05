<?php

use Application\Application;

chdir('..');

require_once 'alien/core/Application.php';
require_once 'alien/init.php';

try {

    header("Content-Type: text/plain; charset=UTF8");

    $confFinfo = new SplFileInfo(__DIR__ . "/../module/Application/config.php");
    $config = new \Alien\Configuration();
    $config->loadConfigurationFromFile($confFinfo);

    $routesFinfo = new \SplFileInfo(__DIR__ . "/../module/Application/routes.php");
    $routesConfig = new \Alien\Configuration();
    $routesConfig->loadConfigurationFromFile($routesFinfo);

    $controllersFinfo = new SplFileInfo(__DIR__ . "/../module/Application/controllers.php");
    $controllersConfig = new \Alien\Configuration();
    $controllersConfig->loadConfigurationFromFile($controllersFinfo);

    $config->mergeWith($routesConfig, $controllersConfig);

    $app = new Application();
    $app->bootstrap($config);

    $app->run();

} catch (\Exception $e) {
    header("Content-Type: text/html; charset=UTF8");
    echo "<h1>Internal Server Error</h1>";
    echo "<strong>" . get_class($e) . ": " . $e->getMessage() . "</strong> at <strong>" . $e->getFile() . "</strong> on line <strong>" . $e->getLine() . "</strong>";
    echo "<h2>Stack trace:</h2>";
    echo "<pre>";
    print_r($e->getTraceAsString());
    echo "</pre>";
}