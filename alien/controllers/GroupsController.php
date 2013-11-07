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

class GroupsController extends BaseController {

    protected function init_action() {

        $this->defaultAction = 'viewList';

        $parentResponse = parent::init_action();
        if ($parentResponse instanceof Response) {
            $data = $parentResponse->getData();
        }

        return new Response(Response::RESPONSE_OK, Array(
            'LeftTitle' => 'Skupiny',
            'ContentLeft' => $this->leftMenuItems(),
            'MainMenu' => $data['MainMenu']
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    private function leftMenuItems() {
        $items = Array();
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('groups', 'viewList'), 'img' => 'group', 'text' => 'Zoznam skupín');
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('groups', 'edit', array('id' => 0)), 'img' => 'add-group', 'text' => 'Pridať/upraviť skupinu');
//        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('groups', 'viewLogs'), 'img' => 'clock', 'text' => 'Posledná aktivita');
        return $items;
    }

    protected function viewList() {
        $view = new View('display/groups/viewList.php', $this);
        $view->groups = Group::getList(true);

        $response = array('Title' => 'Zoznam skupín', 'ContentMain' => $view->renderToString());
        return new Response(Response::RESPONSE_OK, $response, __CLASS__ . '::' . __FUNCTION__);
    }

}
