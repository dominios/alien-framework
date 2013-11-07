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

        return new Response(Response::RESPONSE_OK, Array(
            'LeftTitle' => 'Dashboard',
            'ContentLeft' => '',
            'MainMenu' => $data['MainMenu']
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    protected function home() {

    }

    protected function messages() {
        $view = new View('display/dashboard/viewMessages.php');
        $view->inBox = Message::getListByRecipient(Authorization::getCurrentUser(), true);
        $view->outBox = Message::getListByAuthor(Authorization::getCurrentUser(), true);
        $view->goToMessagePattern = BaseController::actionURL('dashboard', 'messages', array('id' => '%ID%'));
        $view->replyMessagePattern = BaseController::actionURL('dashboard', 'newMessage', array('id' => '%ID%'));
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
        return new Response(Response::RESPONSE_OK, $result, __CLASS__ . '::' . __FUNCTION__);
    }

    protected function newMessage() {
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
        return new Response(Response::RESPONSE_OK, $result, __CLASS__ . '::' . __FUNCTION__);
    }

    protected function sendMessage() {

        if (User::exists($_POST['messageRecipient'])) {
            $initial = array();
            $initial['author'] = Authorization::getCurrentUser()->getId();
            $initial['recipient'] = $_POST['messageRecipient'];
            $initial['message'] = $_POST['messageText'];
            $message = Message::create($initial);
        }

//        var_dump(($_GET['messageRecipient']));
//        die;

        $this->redirect(BaseController::actionURL('dashboard', 'messages'));
    }

    protected function deleteMessage() {

    }

}

