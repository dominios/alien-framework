<?php

class LoginLayout extends AlienLayout {

    const SRC = 'display/login.php';

    const useConsole = false;
    const useNotifications = false;

    public function getPartials(){
        return Array();
    }

    public function handleResponse(ActionResponse $response){
    }
}