<?php

namespace Alien\Controllers;

use Alien\Application;
use Alien\Terminal;
use Alien\Response;

class TerminalController extends BaseController {

    protected function initialize() {
        parent::initialize();
    }

    public function help() {
        $a = get_class_methods('ConsoleController');
        $a = array_diff($a, Array('init_action', 'getContent', '__construct', 'NOP', 'nop', 'phpEval'));
        $a[] = 'php [command]';
        sort($a);
        return implode('<br>', $a);
    }

    public function session() {
        return Terminal::getSuperglobalData('SESSION');
    }

    public function get() {
        return Terminal::getSuperglobalData('GET');
    }

    public function post() {
        return Terminal::getSuperglobalData('POST');
    }

    public function server() {
        return Terminal::getSuperglobalData('SERVER');
    }

    public function cookie() {
        return Terminal::getSuperglobalData('COOKIE');
    }

    public function sdata() {
        return $_SESSION['SDATA'];
    }

    public function clear_sdata() {
        unset($_SESSION['SDATA']);
        return 'SDATA cleared';
    }

    public function phpEval($cmd) {
        //error_reporting(E_ALL);
        $AUTH_NOINIT = true;
        require 'init.php';
        return eval($cmd);
    }

    public function whoami() {
        return 'N/A (WIP)';
    }

    public function groups() {
        return 'N/A (WIP)';
    }

}

?>
