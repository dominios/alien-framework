<?php
ob_start();
session_start();
mb_internal_encoding("UTF-8");
spl_autoload_register('ALiEN_autoloader');

//error_reporting(E_ALL | 2048); // toto je aj so strict, zapnut neskor, teraz to otravuje...
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

//if($AUTH_NOINIT){
    Authorization::getInstance();
//}

function ALiEN_autoloader($class){

    if(preg_match('/^Alien$/', $class)){
        ALiEN_include('class.alien.php');        
        return;
    } elseif(preg_match('/^Alien(.)+/', $class)){
        $filename = preg_split('/Alien/',$class, PREG_SPLIT_DELIM_CAPTURE, PREG_SPLIT_NO_EMPTY);
        ALiEN_include('class.alien.'.strtolower($filename[0]).'.php');
        return;
    }

    AlienLayout::autoloader();
    
    Alien::getInstance()->getConsole()->putMessage('Autoloading class <i>'.$class.'</i>');
    
    if(preg_match('/(.*)Controller$/', $class)){
        $filename = preg_split('/Controller/',$class, PREG_SPLIT_DELIM_CAPTURE, PREG_SPLIT_NO_EMPTY);
        ALiEN_include('./controllers/'.strtolower($filename[0]).'.controller.php');
        return;
    }

    if($class === 'ActionResponse'){
        ALiEN_include('class.action.response.php');
        return;
    }

    if($class === 'FormValidator'){
        ALiEN_include('class.form.validator.php');
        return;
    }
    
    if($class === 'Authorization'){
        ALiEN_include('authorization.php');
        return;
    }

    if($class === 'Notification'){
        ALiEN_include('class.notification.php');
        return;
    }
    
    if(in_array(strtolower($class), array('user', 'group', 'permission'))){
        ALiEN_include('./core/authorization.'.strtolower($class).'.php');
        return;
    }

    if($class === 'FileItem'){
        ALiEN_include('./content/interface.FileItem.php');
        return;
    }
    
    if(preg_match('/^(.)*Item$/', $class)){
        ALiEN_include('./content/class.'.$class.'.php');
        return;
    }
    
    if(preg_match('/^Content(.)*$/', $class)){
        ALiEN_include('./content/class.'.$class.'.php');
        return;
    }

}

function ALiEN_include($file){
    if(file_exists($file)){
        require_once $file; 
    } else {
        throw new Exception('Required file '.$file.' does not exist.');
    }
}
