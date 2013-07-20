<?php

class ConsoleController extends AlienController {        
    
    protected function init_action() {
        parent::init_action();
    }    

    public function help(){
        $a = get_class_methods('ConsoleController');
        $a = array_diff($a, Array('init_action', 'getContent', '__construct', 'NOP', 'nop', 'phpEval'));
        $a[] = 'php [command]';
        sort($a);
        return implode('<br>', $a);
    }
        
    public function session(){
        return AlienConsole::getSuperglobalData('SESSION');
    }  
    
    public function get(){
        return AlienConsole::getSuperglobalData('GET');
    }
    
    public function post(){
        return AlienConsole::getSuperglobalData('POST');
    }
    
    public function server(){
        return AlienConsole::getSuperglobalData('SERVER');
    }

    public function cookie(){
        return AlienConsole::getSuperglobalData('COOKIE');
    }

    public function sdata(){
        return $_SESSION['SDATA'];
    }
    
    public function clear_sdata(){
        unset($_SESSION['SDATA']);
        return 'SDATA cleared';
    }
    
    public function phpEval($cmd){
        return eval($cmd);
    }
    
    public function whoami(){
        return 'N/A (WIP)';
    }
    
    public function groups(){
        return 'N/A (WIP)';
    }
    
    
    
}






?>
