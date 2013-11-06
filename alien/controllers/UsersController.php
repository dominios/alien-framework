<?php

namespace Alien\Controllers;

use Alien\View;
use Alien\Response;
use Alien\Notification;
use Alien\Authorization\User;
use Alien\Authorization\Group;
use Alien\Authorization\Permission;
use Alien\Controllers\BaseController;

class UsersController extends BaseController {

    protected function init_action() {

        $this->defaultAction = 'viewList';

        $parentResponse = parent::init_action();
        if ($parentResponse instanceof Response) {
            $data = $parentResponse->getData();
        }

        return new Response(Response::RESPONSE_OK, Array(
            'LeftTitle' => 'Používatelia',
            'ContentLeft' => $this->leftMenuItems(),
            'MainMenu' => $data['MainMenu']
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    private function leftMenuItems() {
        $items = Array();
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('users', 'viewList'), 'img' => 'user', 'text' => 'Zoznam používateľov');
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('users', 'edit', array('id' => 0)), 'img' => 'add-user', 'text' => 'Pridať/upraviť používateľa');
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('users', 'viewLogs'), 'img' => 'clock', 'text' => 'Posledná aktivita');
        return $items;
    }

    protected function viewList() {
        $view = new View('display/users/viewList.php', $this);
        $view->Users = User::getList(true);
        return new Response(Response::RESPONSE_OK, Array(
            'Title' => 'Zoznam používateľov',
            'ContentMain' => $view->renderToString()
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    protected function edit() {

        if (!preg_match('/^[0-9]*$/', $_GET['id'])) {
            new Notification('Neplatný identifikátor používateľa.', Notification::ERROR);
            return;
        }

        $user = new User((int) $_GET['id']);

        $View = new View('display/users/edit.php', $this);
        $View->Id = (int) $_GET['id'];
        $View->User = $user;
        $View->userGroups = $user->getGroups(true);
        $View->userPermissions = $user->getPermissions(true);
        $View->returnAction = BaseController::actionURL('users', 'viewList');
        $View->resetPasswordAction = BaseController::actionURL('users', 'resetPassword', array('id' => $_GET['id']));
        $View->deleteUserAction = BaseController::actionURL('users', 'removeUser', array('id' => $_GET['id']));

        return new Response(Response::RESPONSE_OK, Array(
            'Title' => (int) $_GET['id'] ? $View->User->getLogin() : 'Nový používateľ',
            'ContentMain' => $View->renderToString()
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    protected function removeUser() {
        if (User::exists($_GET['id'])) {
            $user = new User($_GET['id']);
            $user->delete();
        }
        $this->redirect(BaseController::actionURL('users', 'viewList'));
    }

    protected function addGroup() {
        if (User::exists($_GET['user']) && Group::exists($_GET['group'])) {
            $user = new User($_GET['user']);
            $group = new Group($_GET['group']);
            $user->addGroup($group);
        }
        $this->redirect(BaseController::actionURL('users', 'edit', array('id' => $user->getId())));
    }

    protected function removeGroup() {
        if (User::exists($_GET['user']) && Group::exists($_GET['group'])) {
            $user = new User($_GET['user']);
            $group = new Group($_GET['group']);
            $user->removeGroup($group);
        }
        $this->redirect(BaseController::actionURL('users', 'edit', array('id' => $user->getId())));
    }

    protected function addPermission() {
        if (User::exists($_GET['user']) && Permission::exists($_GET['permission'])) {
            $user = new User($_GET['user']);
            $permission = new Permission($_GET['permission']);
            $user->addPermission($permission);
        }
        $this->redirect(BaseController::actionURL('users', 'edit', array('id' => $user->getId())));
    }

    protected function removePermission() {
        if (User::exists($_GET['user']) && Permission::exists($_GET['permission'])) {
            $user = new User($_GET['user']);
            $permission = new Permission($_GET['permission']);
            $user->removePermission($permission);
        }
        $this->redirect(BaseController::actionURL('users', 'edit', array('id' => $user->getId())));
    }

    protected function resetPassword() {
        if (!preg_match('/^[0-9]*$/', $_GET['id'])) {
            new Notification('Neplatný identifikátor používateľa.', Notification::ERROR);
            return;
        }

        if (User::exists($_GET['id'])) {
            $user = new User($_GET['id']);
            $user->resetPassword();
        }
    }

    protected function userFormSubmit() {

        if (User::exists($_POST['userId'])) {
            $user = new User($_POST['userId']);
        } else {
            $user = User::create(array('email' => $_POST['userEmail']));
        }
        $user->setLogin($_POST['userLogin']);
        $user->setFirstname($_POST['userFirstname']);
        $user->setSurname($_POST['userSurname']);
        $user->setEmail($_POST['userEmail']);
        $user->setStatus($_POST['userStatus']);
        $user->update();

        if ($_POST['userPass2'] === $_POST['userPass3'] && strlen($_POST['userPass2'])) {
            $user->setPassword($_POST['userPass2']);
        }

        $this->redirect(BaseController::actionURL('users', 'edit', array('id' => $user->getId())));
    }

    protected function viewLogs() {
        $view = new View('display/users/logs.php');
        $ret = $view->renderToString();
        return new Response(Response::RESPONSE_OK, Array(
            'Title' => 'Posledná aktivita',
            'ContentMain' => $ret
                ), __CLASS__ . '::' . __FUNCTION__
        );
    }

}
