<?php

namespace Alien\Controllers;

use Alien\Models\Authorization\UserDao;
use Alien\Table\DataTable;
use Alien\View;
use Alien\Response;
use Alien\Notification;
use Alien\FordbiddenException;
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

    /**
     * @var Authorization
     */
    protected $authorization;

    /**
     * @var UserDao
     */
    protected $userDao;

    protected function initialize() {

        $this->defaultAction = 'viewList';

        $parentResponse = parent::initialize();
        if ($parentResponse instanceof Response) {
            $data = $parentResponse->getData();
        }

        $this->authorization = $this->getServiceManager()->getService('Authorization');
        $this->userDao = $this->getServiceManager()->getDao('UserDao');

        return new Response(array(
                'LeftTitle' => 'Používatelia',
                'ContentLeft' => $this->leftMenuItems(),
                'MainMenu' => $data['MainMenu']
            )
        );
    }

    private function leftMenuItems() {
        $items = Array();
        $items[] = Array('permissions' => null, 'url' => BaseController::staticActionURL('users', 'view'), 'img' => 'user', 'text' => 'Zoznam používateľov');
        $items[] = Array('permissions' => null, 'url' => BaseController::staticActionURL('users', 'edit', array('id' => 0)), 'img' => 'add-user', 'text' => 'Pridať/upraviť používateľa');
//        $items[] = Array('permissions' => null, 'url' => BaseController::staticActionURL('users', 'viewLogs'), 'img' => 'clock', 'text' => 'Posledná aktivita');
//        $items[] = Array('permissions' => null, 'url' => BaseController::staticActionURL('users', 'newsletter'), 'img' => 'magazine', 'text' => 'Newsletter');
        return $items;
    }

    protected function viewList() {

        if (!$this->authorization->getCurrentUser()->hasPermission('USER_VIEW')) {
            throw new FordbiddenException('Neodstatočné oprávnenia.');
        }

        $filter = $this->getParam('filter');

        switch ($filter) {
            case 1:
                $zoznam = 'adminov';
                break;
            case 4:
                $zoznam = 'učiteľov';
                break;
            case 3:
                $zoznam = 'študentov';
                break;
            default:
                $zoznam = 'používateľov';
                break;
        }

        $dao = $this->getServiceManager()->getDao('UserDao');
        $data = $dao->getTableData($dao->getList($filter));
        $table = new DataTable($data);
        $table->setName('Zoznam ' . $zoznam);

        if ($this->authorization->getCurrentUser()->hasPermission('USER_ADMIN')) {
            $table->addHeaderColumn(array('edit' => ''));
            $table->addRowColumn(array(
                'edit' => function ($row) {
                        return "<a href=\"/alien/user/edit/$row[id]\">UPRAVIŤ</a>";
                    }
            ));
        };

        $this->view->table = $table;

        return new Response(array(
                'Title' => 'Zoznam ' . $zoznam,
                'ContentMain' => $this->view
            )
        );

    }

    protected function edit() {

        if (!preg_match('/^[0-9]*$/', $_GET['id'])) {
            Notification::error('Neplatný identifikátor používateľa.');
            $this->redirect(BaseController::staticActionURL('users', 'view'));
        }


        if (!$this->authorization->getCurrentUser()->hasPermission(array('USERS_VIEW', 'USER_ADMIN'))) {
            Notification::error('Nedostatočné oprávnenia.');
            $this->redirect(BaseController::staticActionURL('dashboard', 'home'));
        }


        $groups = $this->serviceManager->getDao('GroupDao')->getList();

        $user = $this->userDao->find($this->getParam('id'));

        $form = EditForm::factory($user);

        if ($form->isPostSubmit()) {

            if ($form->validate()) {

                if ($user instanceof User) {

//                if (User::exists($_POST['userId'])) {
//                    $user = new User($_POST['userId']);
//                } else {
//                    $user = User::create(array('email' => $_POST['userEmail']));
//                }
                    $user->setLogin($_POST['userLogin']);
                    $user->setFirstname($_POST['userFirstname']);
                    $user->setSurname($_POST['userSurname']);
                    $user->setEmail($_POST['userEmail']);
                    $user->setStatus($_POST['userStatus']);

                    $this->userDao->update($user);

                    if ($_POST['userPass2'] === $_POST['userPass3'] && strlen($_POST['userPass2'])) {
                        $user->setPassword($_POST['userPass2']);
                    }
                    Notification::success('Zmeny boli uložené.');
                    $this->redirect('/alien/user/edit/' . $user->getId());
                }

            } else {
                Notification::error('Zmeny sa nepodarilo uložiť.');
            }
        }

        $view = new View('display/users/edit.php', $this);
        $view->form = $form;
        $view->user = $user;
        $view->groups = $groups;

        $view->userGroups = $user->getGroups();
        $view->userPermissions = $user->getPermissions(true);

        return new Response(array(
                'Title' => (int) $this->getParam('id') ? $view->user->getLogin() : 'Nový používateľ',
                'ContentMain' => $view->renderToString()
            )
        );
    }

    protected function removeUser() {

        if (!Authorization::getCurrentUser()->hasPermission(array('USERS_VIEW', 'USER_ADMIN'))) {
            Notification::error('Nedostatočné oprávnenia.');
            $this->redirect(BaseController::staticActionURL('dashboard', 'home'));
        }

        if (User::exists($_GET['id'])) {
            $user = new User($_GET['id']);
            $user->delete();
        }
        $this->redirect(BaseController::staticActionURL('users', 'viewList'));
    }

    protected function addGroup() {
        $params = explode('-', $this->getParam('ug'));
        $user = $this->userDao->find($params[0]);
        $group = $this->serviceManager->getDao('GroupDao')->find($params[1]);
        $this->userDao->addGroup($user, $group);
        $this->redirect('/alien/user/edit/' . $user->getId());
    }

    protected function removeGroup() {
        $params = explode('-', $this->getParam('ug'));
        $user = $this->userDao->find($params[0]);
        $group = $this->serviceManager->getDao('GroupDao')->find($params[1]);
        $this->userDao->removeGroup($user, $group);
        $this->redirect('/alien/user/edit/' . $user->getId());
    }

    protected function addPermission() {

        if (!Authorization::getCurrentUser()->hasPermission(array('USERS_VIEW', 'USER_ADMIN'))) {
            Notification::error('Nedostatočné oprávnenia.');
            $this->redirect(BaseController::staticActionURL('dashboard', 'home'));
        }

        if (User::exists($_GET['user']) && Permission::exists($_GET['permission'])) {
            $user = new User($_GET['user']);
            $permission = new Permission($_GET['permission']);
            $user->addPermission($permission);
        }
        $this->redirect(BaseController::staticActionURL('users', 'edit', array('id' => $user->getId())));
    }

    protected function removePermission() {

        if (!Authorization::getCurrentUser()->hasPermission(array('USERS_VIEW', 'USER_ADMIN'))) {
            Notification::error('Nedostatočné oprávnenia.');
            $this->redirect(BaseController::staticActionURL('dashboard', 'home'));
        }

        if (User::exists($_GET['user']) && Permission::exists($_GET['permission'])) {
            $user = new User($_GET['user']);
            $permission = new Permission($_GET['permission']);
            $user->removePermission($permission);
        }
        $this->redirect(BaseController::staticActionURL('users', 'edit', array('id' => $user->getId())));
    }

    protected function resetPassword() {

        if (!Authorization::getCurrentUser()->hasPermission(array('USERS_VIEW', 'USER_ADMIN'))) {
            Notification::error('Nedostatočné oprávnenia.');
            $this->redirect(BaseController::staticActionURL('dashboard', 'home'));
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
        return new Response(array(
                'Title' => 'Posledná aktivita',
                'ContentMain' => $ret
            )
        );
    }

}
