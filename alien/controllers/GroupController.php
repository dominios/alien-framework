<?php

namespace Alien\Controllers;

use Alien\Db\RecordNotFoundException;
use Alien\Forms\Group\EditForm;
use Alien\Forms\Input;
use Alien\Models\Authorization\GroupDao;
use Alien\Table\DataTable;
use Alien\View;
use Alien\Response;
use Alien\Notification;
use Alien\Models\Authorization\Group;
use Alien\Models\Authorization\User;
use Alien\Models\Authorization\Permission;
use Alien\Controllers\BaseController;

class GroupController extends BaseController {

    /**
     * @var GroupDao
     */
    protected $groupDao;

    protected function initialize() {

        $this->groupDao = $this->getServiceManager()->getDao('GroupDao');

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
        $items[] = Array('permissions' => null, 'url' => $this->actionUrl('viewList'), 'img' => 'group', 'text' => 'Zoznam skupín');
        $items[] = Array('permissions' => null, 'url' => $this->actionUrl('edit', array('id' => 0)), 'img' => 'edit', 'text' => 'Pridať/upraviť skupinu');
        return $items;
    }

    protected function view() {

        $data = $this->groupDao->getTableData($this->groupDao->getList());
        $table = new DataTable($data);

        $button = array(
            'type' => 'a',
            'text' => '[E]',
            'class' => '',
            'key' => '%id%',
            'href' => $this->actionUrl('edit', array('id' => '%id%'))
        );

        $table->addButton($button);

        $response = array(
            'Title' => 'Zoznam skupín',
            'ContentMain' => $table
        );

        return new Response($response);
    }

    protected function edit() {

        if (!preg_match('/^[0-9]*$/', $_GET['id'])) {
            Notification::error('Neplatný identifikátor skupiny!', Notification::ERROR);
            return;
        }

        $group = $this->groupDao->find($_GET['id']);
        if (!($group instanceof Group)) {
            throw new RecordNotFoundException();
        }

        $form = EditForm::factory($group);

        if ($form->isPostSubmit()) {
            if ($form->validate()) {
                $group->setName($_POST['groupName']);
                $group->setDescription($_POST['groupDescription']);
                $this->groupDao->update($group);
                $this->redirect($this->actionUrl('view'));
            }
        }

        $view = new View('display/group/edit.php', $this);
        $view->form = $form;
        $view->group = $group;
        $view->returnAction = $this->actionUrl('view');
        $view->deleteAction = $this->actionUrl('remove', array('id' => $_GET['id']));

        $response = array(
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
        $this->redirect(BaseController::staticActionURL('groups', 'edit', array('id' => $group->getId())));
    }

    protected function remove() {
        if (Group::exists($_GET['id'])) {
            $group = new Group($_GET['id']);
            if ($group->isDeletable()) {
                $group->delete();
            }
        }
        $this->redirect(BaseController::staticActionURL('groups', 'view'));
    }

    public function addMember() {
        if (User::exists($_GET['user']) && Group::exists($_GET['group'])) {
            $user = new User($_GET['user']);
            $group = new Group($_GET['group']);
            $user->addGroup($group);
        }
        $this->redirect(BaseController::staticActionURL('groups', 'edit', array('id' => $group->getId())));
    }

    protected function removeMember() {
        if (User::exists($_GET['user']) && Group::exists($_GET['group'])) {
            $user = new User($_GET['user']);
            $group = new Group($_GET['group']);
            $user->removeGroup($group);
        }
        $this->redirect(BaseController::staticActionURL('groups', 'edit', array('id' => $group->getId())));
    }

    protected function addPermission() {
        if (Group::exists($_GET['group']) && Permission::exists($_GET['permission'])) {
            $group = new Group($_GET['group']);
            $permission = new Permission($_GET['permission']);
            $group->addPermission($permission);
        }
        $this->redirect(BaseController::staticActionURL('groups', 'edit', array('id' => $group->getId())));
    }

    protected function removePermission() {
        if (Group::exists($_GET['group']) && Permission::exists($_GET['permission'])) {
            $group = new Group($_GET['group']);
            $permission = new Permission($_GET['permission']);
            $group->removePermission($permission);
        }
        $this->redirect(BaseController::staticActionURL('groups', 'edit', array('id' => $group->getId())));
    }

}
