<?php

namespace Alien\Controllers;

use Alien\Response;
use Alien\View;
use Alien\Message;
use Alien\Models\Authorization\User;
use Alien\Models\Authorization\Authorization;
use Alien\Notification;
use Alien\Forms\Users\EditForm as ProfilForm;

class DashboardController extends BaseController {

    protected function init_action() {
        $this->defaultAction = 'NOP';

        $parentResponse = parent::init_action();
        if ($parentResponse instanceof Response) {
            $data = $parentResponse->getData();
        }

        return new Response(Response::OK, Array(
            'LeftTitle' => 'Dashboard',
            'ContentLeft' => $this->leftMenuItems(),
            'MainMenu' => $data['MainMenu']
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    private function leftMenuItems() {
        $msgText = 'Správy';
        if (Message::getUnreadCount(Authorization::getCurrentUser())) {
            $msgText.= '<span class="badge badge-info badge-right">' . Message::getUnreadCount(Authorization::getCurrentUser()) . ' UNREAD</span>';
        }
        $items = Array();
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('dashboard', 'home'), 'img' => 'dashboard', 'text' => 'Prehľad');
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('dashboard', 'messages'), 'img' => 'message', 'text' => $msgText);
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('dashboard', 'profil'), 'img' => 'user', 'text' => 'Môj profil');
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('base', 'logout'), 'img' => 'logout', 'text' => 'Odhlásiť');
        return $items;
    }

    protected function home() {
//
        Notification::information('test');
        Notification::warning('test');
        Notification::success('test');
        Notification::error('test');
        Notification::accessDenied('test');

        $content = '';
        $view = new View('display/dashboard/home.php');
        $content .= $view->renderToString();
        $result = array('Title' => 'Dashboard', 'ContentMain' => $content);
        return new Response(Response::OK, $result, __CLASS__ . '::' . __FUNCTION__);
    }

    protected function messages() {
        $view = new View('display/dashboard/viewMessages.php');
        $view->inBox = Message::getListByRecipient(Authorization::getCurrentUser(), true);
        $view->outBox = Message::getListByAuthor(Authorization::getCurrentUser(), true);
        $view->goToMessagePattern = BaseController::actionURL('dashboard', 'messages', array('id' => '%ID%'));
        $view->replyMessagePattern = BaseController::actionURL('dashboard', 'composeMessage', array('id' => '%ID%'));
        $view->composeMessageAction = BaseController::actionURL('dashboard', 'composeMessage');
        $view->deleteMessagePattern = BaseController::actionURL('dashboard', 'deleteMessage', array('id' => '%ID%'));
        $message = Message::exists($_GET['id']) ? new Message($_GET['id']) : null;
        if ($message instanceof Message) {
            if ($message->isRecipient(Authorization::getCurrentUser()) && !$message->isSeen()) {
                $message->setDateSeen(time());
                $message->update();
            }
        }
        $view->message = $message;
        $result = array('LeftTitle' => 'Správy', 'Title' => 'Zoznam správ', 'ContentMain' => $view->renderToString());
        return new Response(Response::OK, $result, __CLASS__ . '::' . __FUNCTION__);
    }

    protected function composeMessage() {
        $view = new View('display/dashboard/messageForm.php');
        $view->returnAction = BaseController::actionURL('dashboard', 'messages');
        $view->sender = Authorization::getCurrentUser();
        if (User::exists($_GET['id'])) {
            $user = new User($_GET['id']);
            $view->recipient = $user;
            $title = 'Odpovedať';
        } else {
            $title = 'Nová správa';
        }

        $result = array('LeftTitle' => 'Správy', 'Title' => $title, 'ContentMain' => $view->renderToString());
        return new Response(Response::OK, $result, __CLASS__ . '::' . __FUNCTION__);
    }

    protected function sendMessage() {
        $user = User::getByLogin($_POST['messageRecipient']);
        if ($user instanceof User) {
            $initial = array();
            $initial['author'] = Authorization::getCurrentUser()->getId();
            $initial['recipient'] = $user->getId();
            $initial['message'] = $_POST['messageText'];
            Message::create($initial);
            Notification::success('Správa bola odoslaná.');
        }
        $this->redirect(BaseController::actionURL('dashboard', 'messages'));
    }

    protected function deleteMessage() {
        if (Message::exists($_GET['id'])) {
            $message = new Message($_GET['id']);
            $message->setDeletedByUser(Authorization::getCurrentUser(), true);
            $message->update();
            Notification::success('Správa bola odstránená.');
        }
        $this->redirect(BaseController::actionURL('dashboard', 'messages'));
    }

    protected function profil() {
        $user = Authorization::getCurrentUser();
        $form = ProfilForm::create($user);
        $form->getElement('action')->setValue('dashboard/profil');

        if ($form->isPostSubmit()) {

            if ($form->validate()) {

                if ($_POST['userId'] == Authorization::getCurrentUser()->getId() && User::exists($_POST['userId'])) {
                    $user = Authorization::getCurrentUser();
                } else {
                    Notification::error('Prístup odmiednutý.');
                    $this->redirect(BaseController::actionURL('dashboard', 'profil'));
                    return;
                }
                $user->setLogin($_POST['userLogin']);
                $user->setFirstname($_POST['userFirstname']);
                $user->setSurname($_POST['userSurname']);
                $user->setEmail($_POST['userEmail']);
                $user->update();
                $user->touch();

                if (strlen(trim($_POST['userCurrentPass']))) {
                    if (Authorization::validatePassword($_POST['userCurrentPass'], $user->getPasswordHash())) {
                        if ($_POST['userPass2'] === $_POST['userPass3'] && strlen(trim($_POST['userPass2']))) {
                            $user->setPassword($_POST['userPass2']);
                            Notification::information('Heslo bolo zmenené.');
                            Message::create(array(
                                'author' => 0,
                                'recipient' => $user->getId(),
                                'message' => 'Tvoje heslo bolo s okamžitou platnosťou zmenené.'
                            ));
                        } else {
                            Notification::warning('Niektoré heslo bolo zadané nesprávne, heslo zmenené nebolo.');
                        }
                    } else {
                        Notification::warning('Niektoré heslo bolo zadané nesprávne, heslo zmenené nebolo.');
                    }
                }
                Notification::success('Zmeny boli uložené.');
                $this->redirect(BaseController::actionURL('dashboard', 'profil'));
            } else {
                Notification::error('Zmeny sa nepodarilo uložiť.');
            }
        }


        $view = new View('display/dashboard/profil.php');
        $view->form = $form;
        $view->userGroups = $user->getGroups(true);
        $view->userPermissions = $user->getPermissions(true);

        $result = array('LeftTitle' => 'Môj profil', 'Title' => 'Môj profil', 'ContentMain' => $view->renderToString());
        return new Response(Response::OK, $result, __CLASS__ . '::' . __FUNCTION__);
    }

}
