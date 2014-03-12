<?php

namespace Alien\Controllers;

use Alien\Application;
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

        return new Response(array(
                'LeftTitle' => 'Systém',
                'ContentLeft' => $this->leftMenuItems(),
                'MainMenu' => $data['MainMenu']
            )
        );
    }

    private function leftMenuItems() {
        $items = Array();
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('system', 'logs'), 'img' => 'book-stack', 'text' => 'Logy');
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('system', 'config'), 'img' => 'service', 'text' => 'Konfigurácia');
        return $items;
    }

}
