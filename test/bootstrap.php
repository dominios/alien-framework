<?php

require_once __DIR__ . "/../alien/Psr4Autoloader.php";

$loader = new \Alien\Psr4Autoloader();
$loader->register();
$loader->addNamespace('Alien', __DIR__ . "/../alien/");