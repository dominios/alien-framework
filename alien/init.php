<?php

namespace Alien;

use \Alien\Models\Authorization\Authorization;
use \Alien\Layout\Layout;

ob_start();
session_start();
mb_internal_encoding("UTF-8");
spl_autoload_register('\Alien\class_autoloader', true);

error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
//error_reporting(E_ALL & E_NOTICE & E_STRICT); // toto je aj so strict, zapnut neskor, teraz to otravuje...
Authorization::getInstance();

function class_autoloader($class) {

    if (class_exists($class)) {
        return;
    }

    // vzdy pracuje pod alien priecinkom!
    if (!preg_match('/\/alien/', getcwd()) && file_exists('alien')) {
        chdir('alien');
    }

    // special pluginy; ma mieru
    if ($class == 'GeSHi') {
        include './plugins/geshi.php';
    }
    if ($class == 'PHPMailer') {
        include './plugins/phpmailer/class.phpmailer.php';
    }

    $class = str_replace(__NAMESPACE__ . '\\', '', $class);

    // core sa nacita vzdy cele
    $autoloadDirectories = array();
    $autoloadDirectories[] = './core';
//    $autoloadDirectories[] = './core/authorization';
    $autoloadDirectories[] = './core/form';

    foreach ($autoloadDirectories as $dir) {
        $dh = \opendir($dir);
        if ($dh) {
            while (false !== ($file = readdir($dh))) {
                if (!is_dir($dir . '/' . $file)) {
                    include_once $dir . '/' . $file;
                    ;
                }
            }
            closedir($dh);
        }
    }

    // layouty sa tiez nacitaku staticky vsetky podla tej metody
    Layout::includeSRC();

    // vypis do terminalu co sa kedy autoloaduje
    Alien::getInstance()->getConsole()->putMessage('Autoloading class <i>' . '\\' . $class . '</i>');


    // controllery
    if (preg_match('/(.*)Controller$/', $class)) {
        $class = str_replace('Controllers\\', '', $class);
        $file = './controllers/' . $class . '.php';
        if (file_exists($file)) {
            include_once $file;
        } else {
            Alien::getInstance()->getConsole()->putMessage('Autoloading class failed <i>' . $file . '</i>', Terminal::ERROR);
        }
        return;
    }

    // modely
    if (preg_match('/models/i', $class)) {
        $class = str_replace('Models\\', '', $class);
        $arr = explode('\\', $class, 2);
        $dir = strtolower($arr[0]);
        $class = $arr[1];
        $file = './models/' . $dir . '/' . $class . '.php';
        if (file_exists($file)) {
            include_once $file;
        } else {
            Alien::getInstance()->getConsole()->putMessage('Autoloading class failed <i>' . $file . '</i>', Terminal::ERROR);
            $file = './models/' . $dir . '/' . $class . 'Interface' . '.php';
            if (file_exists($file)) {
                include_once $file;
            }
        }
        return;
    }


    return;
}
