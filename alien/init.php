<?php

namespace Alien;

mb_internal_encoding("UTF-8");

require_once 'core/Psr4Autoloader.php';
$loader = new Psr4Autoloader();
$loader->register();
$loader->addNamespace('Alien', __DIR__ . '/core');
$loader->addNamespace('Application', __DIR__ . '/../module/Application');

error_reporting(E_ALL & ~E_NOTICE);

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $severity =
        1 * E_ERROR |
        1 * E_WARNING |
        1 * E_PARSE |
        0 * E_NOTICE |
        1 * E_CORE_ERROR |
        1 * E_CORE_WARNING |
        1 * E_COMPILE_ERROR |
        1 * E_COMPILE_WARNING |
        1 * E_USER_ERROR |
        1 * E_USER_WARNING |
        0 * E_USER_NOTICE |
        0 * E_STRICT |
        1 * E_RECOVERABLE_ERROR |
        1 * E_DEPRECATED |
        1 * E_USER_DEPRECATED;
    $ex = new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    if (($ex->getSeverity() & $severity) != 0) {
        throw $ex;
    }
});

// @todo urobit samostatnu zlozku a loadovat z configu
include_once 'functions.php';
