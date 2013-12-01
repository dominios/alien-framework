<?php

namespace Alien\Layout;

use Alien\Response;

class LoginLayout extends Layout {

    const SRC = 'display/layouts/login/index.php';
    const useConsole = false;
    const useNotifications = false;

    public function getPartials() {
        return Array();
    }

    public function handleResponse(Response $response) {

    }

}

