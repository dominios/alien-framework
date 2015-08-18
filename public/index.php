<?php


use Application\Application;

chdir('..');

require_once 'alien/core/Application.php';
require_once 'alien/init.php';


// test routingu

$urls = [
//    "/route",
//    "/route/value1",
    "/route/sub",
//    "/route/sub/p1/p2",
//    "/example/sub/p1/p2",
];

try {

    header("Content-Type: text/plain; charset=UTF8");

    $confFinfo = new SplFileInfo(__DIR__ . '/../alien/config.php');
    $config = new \Alien\Configuration();
    $config->loadConfigurationFromFile($confFinfo);

    $app = new Application();
    $app->bootstrap($config);

    /* @var Alien\Routing\Router $router */
    $router = $app->getServiceLocator()->getService('Router');

    foreach($urls as $u) {
        echo "Test URL: " . $u . "\n";
        try {
            $match = $router->getMatchedConfiguration($u);
            echo "Match: ";
            echo PHP_EOL .  print_r($match);
            echo "\n";
        } catch (\Alien\Routing\Exception\RouteNotFoundException $e) {
            echo "Route not found\n";
        }
        echo "\n";
    }

    $app->run();

} catch (\Exception $e) {
    echo "<h1>Internal Server Error</h1>";
    echo "<strong>" . get_class($e) . ": " . $e->getMessage() . "</strong> at <strong>" . $e->getFile() . "</strong> on line <strong>" . $e->getLine() . "</strong>";
    echo "<h2>Stack trace:</h2>";
    echo "<pre>";
    print_r($e->getTraceAsString());
    echo "</pre>";
}