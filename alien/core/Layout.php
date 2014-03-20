<?php

namespace Alien\Layout;

use Alien\Application;
use Alien\Models\Content\Widget;
use Alien\View;
use Alien\Response;
use Alien\NotificationContainer;
use Alien\Models\Authorization\Authorization;

abstract class Layout {

    const SRC = '';
    const useNotifications = false;
    const useConsole = false;

    protected $notificationContainer = null;
    protected $terminal;
    protected $metaScripts = array();
    protected $metaStylesheets = array();

    /**
     * TODO error vyriesit cez exception, tato logika tu nepatri
     */
    public function __construct() {

        $Class = get_called_class();

        if (file_exists($Class::SRC)) {
            Application::getInstance()->getConsole()->putMessage('Using <i>' . get_called_class() . '</i>.');
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            die(__CLASS__ . '::' . __FUNCTION__ . ' - Bad SRC ' . $Class::SRC);
        }
    }

    public abstract function getPartials();

    public abstract function handleResponse(Response $response);

    public final function __toString() {

        $Class = get_called_class();

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

        $scripts = '';
        foreach ($this->metaScripts as $script) {
            $scripts .= "<script type=\"$script[type]\" src=\"$script[src]\"></script>\n";
        }
        $view->metaScripts = $scripts;
        $styles = '';
        foreach ($this->metaStylesheets as $style) {
            $styles .= "<link type=\"$style[type]\"  href=\"$style[href]\" rel=\"$style[rel]\">\n";
        }
        $view->metaStylesheets = $styles;

        if ($Class::useConsole) {
            if ((true || Application::getParameter('debugMode')) && Authorization::getCurrentUser()->getId()) {
                $console = new View('display/system/console.php', null);
                $console->messages = Application::getInstance()->getConsole()->getMessageList();
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

    protected function appendStylesheet($href, $type = null, $rel = null) {
        $style = array(
            'href' => $href,
            'type' => is_null($type) ? 'text/css' : $type,
            'rel' => is_null($rel) ? 'stylesheet' : $rel
        );
        array_push($this->metaStylesheets, $style);
    }

    protected function prependStylesheet($href, $type = null, $rel = null) {
        $style = array(
            'href' => $href,
            'type' => is_null($type) ? 'text/css' : $type,
            'rel' => is_null($rel) ? 'stylesheet' : $rel
        );
        array_unshift($this->metaStylesheets, $style);
    }

    protected function appendScript($src, $type = null) {
        $script = array(
            'src' => $src,
            'type' => is_null($type) ? 'text/javascript' : $type
        );
        array_push($this->metaScripts, $script);
    }

    protected function prependScript($src, $type = null) {
        $script = array(
            'src' => $src,
            'type' => is_null($type) ? 'text/javascript' : $type
        );
        array_unshift($this->metaScripts, $script);
    }
}
