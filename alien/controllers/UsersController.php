<?php

namespace Alien\Controllers;

use Alien\View;
use Alien\Response;
use Alien\Notification;
use Alien\Models\Authorization\User;
use Alien\Models\Authorization\Group;
use Alien\Models\Authorization\Permission;
use Alien\Models\Authorization\Authorization;
use Alien\Controllers\BaseController;
use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Forms\Validator;
use Alien\Forms\Users\EditForm;

class UsersController extends BaseController {

    protected function init_action() {

        $this->defaultAction = 'viewList';

        $parentResponse = parent::init_action();
        if ($parentResponse instanceof Response) {
            $data = $parentResponse->getData();
        }

        return new Response(Response::OK, Array(
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
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('users', 'newsletter'), 'img' => 'magazine', 'text' => 'Newsletter');
        return $items;
    }

    protected function viewList() {

        if (!Authorization::getCurrentUser()->hasPermission('USER_VIEW')) {
            Notification::error('Nedostatočné oprávnenia.');
            $this->redirect(BaseController::actionURL('dashboard', 'home'));
        }

        $view = new View('display/users/viewList.php', $this);
        $view->users = User::getList(true);
        $view->editActionPattern = BaseController::actionURL('users', 'edit', array('id' => '%ID%'));
        $view->sendMessagePattern = BaseController::actionURL('dashboard', 'composeMessage', array('id' => '%ID%'));
        return new Response(Response::OK, Array(
            'Title' => 'Zoznam používateľov',
            'ContentMain' => $view->renderToString()
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    protected function edit() {

        if (!preg_match('/^[0-9]*$/', $_GET['id'])) {
            Notification::error('Neplatný identifikátor používateľa.');
            $this->redirect(BaseController::actionURL('users', 'view'));
        }

        if (!Authorization::getCurrentUser()->hasPermission(array('USERS_VIEW', 'USER_ADMIN'))) {
            Notification::error('Nedostatočné oprávnenia.');
            $this->redirect(BaseController::actionURL('dashboard', 'home'));
        }

        $user = new User((int) $_GET['id']);

        $form = EditForm::create($user);

        if ($form->isPostSubmit()) {

            if ($form->validate()) {

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
                Notification::success('Zmeny boli uložené.');
                $this->redirect(BaseController::actionURL('users', 'edit', array('id' => $user->getId())));
            } else {
                Notification::error('Zmeny sa nepodarilo uložiť.');
            }
        }

        $view = new View('display/users/edit.php', $this);
        $view->form = $form;
        $view->user = $user;

        $view->userGroups = $user->getGroups(true);
        $view->userPermissions = $user->getPermissions(true);

        return new Response(Response::OK, Array(
            'Title' => (int) $_GET['id'] ? $view->user->getLogin() : 'Nový používateľ',
            'ContentMain' => $view->renderToString()
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    protected function removeUser() {

        if (!Authorization::getCurrentUser()->hasPermission(array('USERS_VIEW', 'USER_ADMIN'))) {
            Notification::error('Nedostatočné oprávnenia.');
            $this->redirect(BaseController::actionURL('dashboard', 'home'));
        }

        if (User::exists($_GET['id'])) {
            $user = new User($_GET['id']);
            $user->delete();
        }
        $this->redirect(BaseController::actionURL('users', 'viewList'));
    }

    protected function addGroup() {

        if (!Authorization::getCurrentUser()->hasPermission(array('USERS_VIEW', 'USER_ADMIN'))) {
            Notification::error('Nedostatočné oprávnenia.');
            $this->redirect(BaseController::actionURL('dashboard', 'home'));
        }

        if (User::exists($_GET['user']) && Group::exists($_GET['group'])) {
            $user = new User($_GET['user']);
            $group = new Group($_GET['group']);
            $user->addGroup($group);
        }
        $this->redirect(BaseController::actionURL('users', 'edit', array('id' => $user->getId())));
    }

    protected function removeGroup() {

        if (!Authorization::getCurrentUser()->hasPermission(array('USERS_VIEW', 'USER_ADMIN'))) {
            Notification::error('Nedostatočné oprávnenia.');
            $this->redirect(BaseController::actionURL('dashboard', 'home'));
        }

        if (User::exists($_GET['user']) && Group::exists($_GET['group'])) {
            $user = new User($_GET['user']);
            $group = new Group($_GET['group']);
            $user->removeGroup($group);
        }
        $this->redirect(BaseController::actionURL('users', 'edit', array('id' => $user->getId())));
    }

    protected function addPermission() {

        if (!Authorization::getCurrentUser()->hasPermission(array('USERS_VIEW', 'USER_ADMIN'))) {
            Notification::error('Nedostatočné oprávnenia.');
            $this->redirect(BaseController::actionURL('dashboard', 'home'));
        }

        if (User::exists($_GET['user']) && Permission::exists($_GET['permission'])) {
            $user = new User($_GET['user']);
            $permission = new Permission($_GET['permission']);
            $user->addPermission($permission);
        }
        $this->redirect(BaseController::actionURL('users', 'edit', array('id' => $user->getId())));
    }

    protected function removePermission() {

        if (!Authorization::getCurrentUser()->hasPermission(array('USERS_VIEW', 'USER_ADMIN'))) {
            Notification::error('Nedostatočné oprávnenia.');
            $this->redirect(BaseController::actionURL('dashboard', 'home'));
        }

        if (User::exists($_GET['user']) && Permission::exists($_GET['permission'])) {
            $user = new User($_GET['user']);
            $permission = new Permission($_GET['permission']);
            $user->removePermission($permission);
        }
        $this->redirect(BaseController::actionURL('users', 'edit', array('id' => $user->getId())));
    }

    protected function resetPassword() {

        if (!Authorization::getCurrentUser()->hasPermission(array('USERS_VIEW', 'USER_ADMIN'))) {
            Notification::error('Nedostatočné oprávnenia.');
            $this->redirect(BaseController::actionURL('dashboard', 'home'));
        }

        if (!preg_match('/^[0-9]*$/', $_GET['id'])) {
            Notification::error('Neplatný identifikátor používateľa');
            return;
        }

        if (User::exists($_GET['id'])) {
            $user = new User($_GET['id']);
            $user->resetPassword();
        }
    }

    protected function viewLogs() {
        $view = new View('display/users/logs.php');
        $ret = $view->renderToString();
        return new Response(Response::OK, Array(
            'Title' => 'Posledná aktivita',
            'ContentMain' => $ret
                ), __CLASS__ . '::' . __FUNCTION__
        );
    }

}
