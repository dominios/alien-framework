<?php

namespace Alien\Layot;

use Alien\Response;

class LoginLayout extends Layout {

    const SRC = 'display/login.php';
    const useConsole = false;
    const useNotifications = false;

    public function getPartials() {
        return Array();
    }

    public function handleResponse(Response $response) {

    }

}

