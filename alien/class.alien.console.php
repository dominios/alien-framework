<?php

class AlienConsole {
    
    const CONSOLE_MSG = 'console_msg';
    const CONSOLE_WARNING = 'console_warning';
    const CONSOLE_ERROR = 'console_error';
    
    private static $instance = null;
    private $messages;
    
    private function __construct() {
        $this->messages = Array();
        $this->messages[] = Array('time'=>time(), 'msg'=>'Console started.');
    }
    
    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function putMessage($msg, $level=self::CONSOLE_MSG){
        $m = Array();
        $m['time'] = time();
        $m['msg'] = $msg;
        $m['level'] = $level;
        $this->messages[] = $m;
    }
    
    public function getMessageList(){
        return $this->messages;
    }

    public static function getSuperglobalData($globalArray){
        $ret = '';
        switch($globalArray){
            case 'SESSION': $arr = $_SESSION; break;
            case 'POST': $arr = $_POST; break;
            case 'GET' : $arr = $_GET; break;
            case 'SERVER' : $arr = $_SERVER; break;
            case 'COOKIE' : $arr = $_COOKIE; break;
            default : return '';
        }
        foreach($arr as $key => $value){
            $ret .= $key.' => '.$value.'<br>';
        }
        return $ret;
    }
    
}
?>
