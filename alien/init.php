<?php

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

include_once 'functions.php';

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

    $class = str_replace(__NAMESPACE__ . '\\', '', $class);

    // core sa nacita vzdy cele
    $autoloadDirectories = array();
    $autoloadDirectories[] = 'core';
    $autoloadDirectories[] = 'core/Di';
    $autoloadDirectories[] = 'core/Di/Exception';
    $autoloadDirectories[] = 'core/Form';
    $autoloadDirectories[] = 'core/Form/Input';
    $autoloadDirectories[] = 'core/Form/Validator';
    $autoloadDirectories[] = 'core/table';
    $autoloadDirectories[] = 'layouts';
    $autoloadDirectories[] = 'core/Db';
//    $autoloadDirectories[] = 'core/annotation';

    foreach ($autoloadDirectories as $dir) {
        $dh = \opendir($dir);
        if ($dh) {
            while (false !== ($file = readdir($dh))) {
                if (!is_dir($dir . '/' . $file)) {
                    include_once $dir . '/' . $file;;
                }
            }
            closedir($dh);
        }
    }

    // controllery
    if (preg_match('/(.*)Controller$/', $class)) {
        $class = str_replace('Controllers\\', '', $class);
        $file = 'controllers/' . $class . '.php';
        if (file_exists($file)) {
            include_once $file;
        }
        return;
    }

    // modely
    if (preg_match('/models/i', $class)) {
        $class = str_replace('Models\\', '', $class);
        $arr = explode('\\', $class, 2);
        $dir = strtolower($arr[0]);
        $class = $arr[1];
        $file = 'models/' . $dir . '/' . $class . '.php';
        if (file_exists($file)) {
            include_once $file;
        } else {
            $file = 'models/' . $dir . '/' . $class . 'Interface' . '.php';
            if (file_exists($file)) {
                include_once $file;
            }
        }
        return;
    }

    // formulare
    if (preg_match('/form/i', $class)) {
        $class = str_replace('Forms\\', '', $class);
        $arr = explode('\\', $class, 2);
        $dir = strtolower($arr[0]);
        $class = $arr[1];
        $file = 'forms/' . $dir . '/' . $class . '.php';
        if (file_exists($file)) {
            include_once $file;
        }
        return;
    }

    return;
}