<?php

// $loader = require __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, "/../vendor/autoload.php");

$loader = require implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'vendor', 'autoload.php']);
$loader->add('Alien', implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Alien']));