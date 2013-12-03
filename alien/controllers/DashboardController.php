<?php

namespace Alien\Controllers;

use Alien\Response;
use Alien\View;
use Alien\Message;
use Alien\Authorization\User;
use Alien\Authorization\Authorization;

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
            $msgText.= ' (' . Message::getUnreadCount(Authorization::getCurrentUser()) . ')';
        }
        $items = Array();
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('dashboard', 'home'), 'img' => 'dashboard', 'text' => 'Prehľad');
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('dashboard', 'messages'), 'img' => 'message', 'text' => $msgText);
        return $items;
    }

    protected function home() {
        $view = new View('display/dashboard/home.php');

        $result = array('Title' => 'Dashboard', 'ContentMain' => $view->renderToString());
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
        }
        $this->redirect(BaseController::actionURL('dashboard', 'messages'));
    }

    protected function deleteMessage() {
        if (Message::exists($_GET['id'])) {
            $message = new Message($_GET['id']);
            $message->setDeletedByUser(Authorization::getCurrentUser(), true);
            $message->update();
        }
        $this->redirect(BaseController::actionURL('dashboard', 'messages'));
    }

}

