<?php

namespace Alien\Controllers;

use Alien\Alien;
use Alien\View;
use Alien\Response;
use Alien\Notification;
use Alien\Authorization\User;
use Alien\Authorization\Group;
use Alien\Authorization\Permission;
use Alien\Controllers\BaseController;

class SystemController extends BaseController {

    protected function init_action() {

        $parentResponse = parent::init_action();
        if ($parentResponse instanceof Response) {
            $data = $parentResponse->getData();
        }

        return new Response(Response::OK, Array(
            'LeftTitle' => 'Systém',
            'ContentLeft' => '',
            'MainMenu' => $data['MainMenu']
                ), __CLASS__ . '::' . __FUNCTION__);
    }

}
