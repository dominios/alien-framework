<?php
/**
 * inicializacia
 * @todo vsetko co sa da nech ide z Application::bootstrap() tento subor by mal obsahovat minimum logiky
 */


namespace Alien;

use ErrorException;

ob_start();
session_start();
mb_internal_encoding("UTF-8");
spl_autoload_register('\Alien\class_autoloader', true);

//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);

function exception_error_handler($errno, $errstr, $errfile, $errline) {
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
    $ex = new ErrorException($errstr, 0, $errno, $errfile, $errline);
    if (($ex->getSeverity() & $severity) != 0) {
        throw $ex;
    }
}

set_error_handler("\\Alien\\exception_error_handler");

// vzdy pracuje pod alien priecinkom!
if (!preg_match('/\/alien$/', getcwd()) && file_exists('alien')) {
    chdir('alien');
}

// @todo urobit samostatnu zlozku a loadovat z configu
include_once 'functions.php';

/**
 * @param $class string Class name
 * @todo PSR-4 autoloader
 */
function class_autoloader($class) {

    if (class_exists($class)) {
        return;
    }

    // vzdy pracuje pod alien priecinkom!
    if (!preg_match('/\/alien$/', getcwd()) && file_exists('alien')) {
        chdir('alien');
    }

    // special pluginy; ma mieru
    if ($class == 'GeSHi') {
        include 'plugins/geshi.php';
    }
    if ($class == 'PHPMailer') {
        include 'plugins/phpmailer/class.phpmailer.php';
    }

    if(strpos($class, __NAMESPACE__) !== false) {
        $searchedFile = getcwd() . '\core\\' . str_replace(__NAMESPACE__ . '\\', '', $class) . '.php';
        if (file_exists($searchedFile)) {
            require_once $searchedFile;
            return;
        }
    }

    // ide o modul
    $parts = explode('/', '$class');
    $modul = $parts[0];
    $searchedFile = '..\module\\' . $class . '.php';
    if(file_exists($searchedFile)) {
        require_once $searchedFile;
    }

    return;
}