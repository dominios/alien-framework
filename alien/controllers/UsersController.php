<?php

namespace Alien\Controllers;

use Alien\View;
use Alien\Response;
use Alien\Notification;
use Alien\Authorization\User;
use Alien\Authorization\Group;
use Alien\Authorization\Permission;
use Alien\Authorization\Authorization;
use Alien\Controllers\BaseController;
use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Forms\Validator;

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

        $form = new Form('post', '', 'editUserForm');
        $form->setId('userForm');
        $inputAction = Input::hidden('action', 'users/' . __FUNCTION__)->addToForm($form);

        $inputLogin = Input::text('userLogin', '', $user->getLogin())->setAutocomplete(false)->addToForm($form);
        $inputFirstname = Input::text('userFirstname', '', $user->getFirstname())->setAutocomplete(false)->addToForm($form);
        $inputSurname = Input::text('userSurname', '', $user->getSurname())->setAutocomplete(false)->addToForm($form);
        $inputEmail = Input::text('userEmail', '', $user->getEmail())
                ->setAutocomplete(false)
                ->addValidator(Validator::regexp(Validator::PATTERN_EMAIL, 'neplatná adresa'))
                ->addValidator(Validator::custom('userUniqueEmail', array('ignoredUserId' => $user->getId()), 'tento email sa už používa'))
                ->addToForm($form);
        $inputPass2 = Input::password('userPass2', '')->addToForm($form);
        $inputPass3 = Input::password('userPass3', '')->addToForm($form);

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
        $view->user = $user;
        $view->formStartTag = $form->startTag();
        $view->formEndTag = $form->endTag();
        $view->buttonCancel = Input::button(BaseController::actionURL('users', 'viewList'), 'Zrušiť', 'icon-back');
        $view->buttonSave = Input::button("javascript: $('#userForm').submit();", 'Uložiť', 'icon-save');
        $view->buttonMessage = Input::button(BaseController::actionURL('dashboard', 'composeMessage', array('id' => $_GET['id'])), 'Poslať správu', 'icon-message');
        $view->buttonResetPassword = Input::button(BaseController::actionURL('users', 'resetPassword', array('id' => $_GET['id'])), 'Resetovať heslo', 'icon-shield')->setDisabled(true);
        $view->buttonDelete = Input::button(BaseController::actionURL('users', 'removeUser', array('id' => $_GET['id'])), 'Odstrániť používateľa', 'icon-delete')->setDisabled(true);
        $view->inputAction = $inputAction;
        $view->inputEmail = $inputEmail;
        $view->inputLogin = $inputLogin;
        $view->inputFirstname = $inputFirstname;
        $view->inputSurname = $inputSurname;
        $view->inputPass2 = $inputPass2;
        $view->inputPass3 = $inputPass3;
        $view->userGroups = $user->getGroups(true);
        $view->userPermissions = $user->getPermissions(true);

        $view->buttonAddGroup = Input::button('javascript: userShowAddGroupDialog(' . $user->getId() . ');', 'Pridať skupinu', 'icon-plus');
        $view->buttonAddPermission = Input::button('javascript: userShowAddPermissionDialog(' . $user->getId() . ');', 'Pridať oprávnenie', 'icon-plus');

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
            new Notification('Neplatný identifikátor používateľa.', Notification::ERROR);
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
