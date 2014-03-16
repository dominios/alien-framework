<?php

namespace Alien\Controllers;

use Alien\View;
use Alien\Response;
use Alien\Notification;
use Alien\Models\Authorization\Group;
use Alien\Models\Authorization\User;
use Alien\Models\Authorization\Permission;
use Alien\Controllers\BaseController;

class GroupsController extends BaseController {

    protected function initialize() {

        $this->defaultAction = 'viewList';

        $parentResponse = parent::initialize();
        if ($parentResponse instanceof Response) {
            $data = $parentResponse->getData();
        }

        return new Response(array(
                'LeftTitle' => 'Skupiny',
                'ContentLeft' => $this->leftMenuItems(),
                'MainMenu' => $data['MainMenu']
            )
        );
    }

    private function leftMenuItems() {
        $items = Array();
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('groups', 'viewList'), 'img' => 'group', 'text' => 'Zoznam skupín');
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('groups', 'edit', array('id' => 0)), 'img' => 'edit', 'text' => 'Pridať/upraviť skupinu');
//        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('groups', 'viewLogs'), 'img' => 'clock', 'text' => 'Posledná aktivita');
        return $items;
    }

    protected function viewList() {
        $view = new View('display/groups/viewList.php', $this);
        $view->groups = Group::getList(true);

        $response = array('Title' => 'Zoznam skupín', 'ContentMain' => $view->renderToString());
        return new Response($response);
    }

    protected function edit() {
        if (!preg_match('/^[0-9]*$/', $_GET['id'])) {
            Notification::error('Neplatný identifikátor skupiny!', Notification::ERROR);
            return;
        }
        $view = new View('display/groups/edit.php', $this);
        $group = new Group((int) $_GET['id']);
        $view->group = $group;
        $view->returnAction = BaseController::actionURL('groups', 'viewList');
        $view->deleteAction = BaseController::actionURL('groups', 'remove', array('id' => $_GET['id']));

        $response = Array(
            'Title' => (int) $_GET['id'] ? $group->getName() : 'Nová skupina',
            'ContentMain' => $view->renderToString()
        );
        return new Response($response);
    }

    protected function groupFormSubmit() {
        if (Group::exists($_POST['groupId'])) {
            $group = new Group($_POST['groupId']);
        } else {
            $initialValues = array();
            $group = Group::create($initialValues);
        }
        $group->setName($_POST['groupName']);
        $group->setDescription($_POST['groupDescription']);
        $group->update();
        $this->redirect(BaseController::actionURL('groups', 'edit', array('id' => $group->getId())));
    }

    protected function remove() {
        if (Group::exists($_GET['id'])) {
            $group = new Group($_GET['id']);
            if ($group->isDeletable()) {
                $group->delete();
            }
        }
        $this->redirect(BaseController::actionURL('groups', 'viewList'));
    }

    public function addMember() {
        if (User::exists($_GET['user']) && Group::exists($_GET['group'])) {
            $user = new User($_GET['user']);
            $group = new Group($_GET['group']);
            $user->addGroup($group);
        }
        $this->redirect(BaseController::actionURL('groups', 'edit', array('id' => $group->getId())));
    }

    protected function removeMember() {
        if (User::exists($_GET['user']) && Group::exists($_GET['group'])) {
            $user = new User($_GET['user']);
            $group = new Group($_GET['group']);
            $user->removeGroup($group);
        }
        $this->redirect(BaseController::actionURL('groups', 'edit', array('id' => $group->getId())));
    }

    protected function addPermission() {
        if (Group::exists($_GET['group']) && Permission::exists($_GET['permission'])) {
            $group = new Group($_GET['group']);
            $permission = new Permission($_GET['permission']);
            $group->addPermission($permission);
        }
        $this->redirect(BaseController::actionURL('groups', 'edit', array('id' => $group->getId())));
    }

    protected function removePermission() {
        if (Group::exists($_GET['group']) && Permission::exists($_GET['permission'])) {
            $group = new Group($_GET['group']);
            $permission = new Permission($_GET['permission']);
            $group->removePermission($permission);
        }
        $this->redirect(BaseController::actionURL('groups', 'edit', array('id' => $group->getId())));
    }

}
