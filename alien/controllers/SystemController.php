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
            'ContentLeft' => $this->leftMenuItems(),
            'MainMenu' => $data['MainMenu']
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    private function leftMenuItems() {
        $items = Array();
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('system', 'logs'), 'img' => 'book-stack', 'text' => 'Logy');
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('system', 'config'), 'img' => 'service', 'text' => 'Konfigurácia');
        return $items;
    }

}
