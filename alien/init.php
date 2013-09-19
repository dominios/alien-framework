<?php

namespace Alien;

use \Alien\Authorization\Authorization;
use \Alien\Layot\Layout;

ob_start();
session_start();
mb_internal_encoding("UTF-8");
spl_autoload_register('\Alien\ALiEN_autoloader', true);

//error_reporting(E_ALL | 2048); // toto je aj so strict, zapnut neskor, teraz to otravuje...
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
//
//if($AUTH_NOINIT){
Authorization::getInstance();
//}

/**
 * TODO : nejako normalnejsie, teraz po namespacoch je tu BORDEL !!!
 */
function ALiEN_autoloader($class) {

    // vzdy pracuje pod alien priecinkom!
    if (!preg_match('/alien/', getcwd())) {
        chdir('alien');
    }

    if (class_exists($class)) {
        return;
    }

    $class = str_replace(__NAMESPACE__ . '\\', '', $class);

    // core sa nacita vzdy cele
    if ($dh = \opendir('./core')) {
        while (false !== ($file = readdir($dh))) {
            if (!is_dir('./core/' . $file)) {
                include_once './core/' . $file;
            }
        }
        closedir($dh);
    }
    if ($dh = \opendir('./core/authorization')) {
        while (false !== ($file = readdir($dh))) {
            if (!is_dir('./core/authorization/' . $file)) {
                include_once './core/authorization/' . $file;
            }
        }
        closedir($dh);
    }

    // layouty sa tiez nacitaku staticky vsetky podla tej metody
    Layout::autoloader();

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
        include_once $file;
        return;
    }


    return;


    if ($class == 'BaseController') {
        include './core/class.BaseController.php';
    }

    if (preg_match('/^Alien$/', $class)) {
        ALiEN_include('class.alien.php');
        return;
    } elseif (preg_match('/^Alien(.)+/', $class)) {
        $filename = preg_split('/Alien/', $class, PREG_SPLIT_DELIM_CAPTURE, PREG_SPLIT_NO_EMPTY);
        ALiEN_include('class.alien.' . strtolower($filename[0]) . '.php');
        return;
    }

    Layout::autoloader();

    Alien::getInstance()->getConsole()->putMessage('Autoloading class <i>' . $class . '</i>');

    if (preg_match('/(.*)Controller$/', $class)) {
        $filename = preg_split('/Controller/', $class, PREG_SPLIT_DELIM_CAPTURE, PREG_SPLIT_NO_EMPTY);
        ALiEN_include('./controllers/' . strtolower($filename[0]) . '.controller.php');
        return;
    }

    if ($class === 'ActionResponse') {
        ALiEN_include('class.action.response.php');
        return;
    }

    if ($class === 'FormValidator') {
        ALiEN_include('class.form.validator.php');
        return;
    }

    if ($class === 'Authorization') {
        ALiEN_include('authorization.php');
        return;
    }

    if ($class === 'Notification') {
        ALiEN_include('class.notification.php');
        return;
    }

    if (in_array(strtolower($class), array('user', 'group', 'permission'))) {
        ALiEN_include('./core/authorization.' . strtolower($class) . '.php');
        return;
    }

    if ($class === 'FileItem') {
        ALiEN_include('./modules/content/interface.FileItem.php');
        return;
    }

    if (preg_match('/^(.)*Item$/', $class)) {
        ALiEN_include('./modules/content/class.' . $class . '.php');
        return;
    }

    if (preg_match('/^(.)*ItemView$/', $class)) {
        ALiEN_include('./modules/content/class.' . $class . '.php');
        return;
    }

    if (preg_match('/^Content(.)*$/', $class)) {
        ALiEN_include('./modules/content/class.' . $class . '.php');
        return;
    }
}

function ALiEN_include($file) {
    $file = str_replace('\\', '', $file);
    if (!preg_match('/alien/', getcwd())) {
//        $file = 'alien/'.$file;
        chdir('alien');
    }
    if (file_exists($file)) {
        require_once $file;
    } else {
        throw new Exception('Required file ' . $file . ' does not exist.');
    }
}
