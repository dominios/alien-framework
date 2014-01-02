<?php

namespace Alien\Layout;

use Alien\Alien;
use Alien\View;
use Alien\Response;
use Alien\NotificationContainer;
use Alien\Models\Authorization\Authorization;

abstract class Layout {

    const SRC = '';
    const useNotifications = false;
    const useConsole = false;

    protected $notificationContainer = null;
    protected $console;

    public function __construct() {

        $Class = get_called_class();

        if (file_exists($Class::SRC)) {
            Alien::getInstance()->getConsole()->putMessage('Using <i>' . get_called_class() . '</i>.');
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            die(__CLASS__ . '::' . __FUNCTION__ . ' - Bad SRC ' . $Class::SRC);
        }
    }

    public static final function includeSRC() {
        include_once 'layouts/IndexLayout.php';
        include_once 'layouts/LoginLayout.php';
    }

    public abstract function getPartials();

    public abstract function handleResponse(Response $response);

    public final function renderToString() {

        $Class = get_called_class();
//        $view = new View($Class::SRC, null);

        $view = new View($this->getSRC());

        $view->setAutoEscape(false);
        $view->setAutoStripTags(false);

        if ($Class::useNotifications && $this->notificationContainer instanceof NotificationContainer) {
            $list = $this->notificationContainer->getNotifications();
            if (sizeof($list)) {
                $partialView = new View('display/system/notifications.php', null);
                $partialView->list = $list;
                $partialViewStr = $partialView->renderToString();
                $this->notificationContainer->flushNotifications();
            } else {
                $partialViewStr = '';
            }
            $view->notifications = $partialViewStr;
        }

        $partials = $this->getPartials();
        if (sizeof($partials)) {
            foreach ($partials as $k => $v) {
                $view->$k = $v;
            }
        }

        if ($Class::useConsole) {
            if ((true || Alien::getParameter('debugMode')) && Authorization::getCurrentUser()->getId()) {
                $console = new View('display/system/console.php', null);
                $console->messages = Alien::getInstance()->getConsole()->getMessageList();
                $view->terminal = $console->renderToString();
            }
        }

        $content = $view->renderToString();

        return $content;
    }

    public function setNotificationContainer(NotificationContainer $container) {
        $this->notificationContainer = $container;
    }

    protected function getSRC() {
        $class = get_called_class();
        return $class::SRC;
    }

}
