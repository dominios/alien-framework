<?php

abstract class AlienLayout {

    const SRC = '';

    const useNotifications = false;
    protected $notifications = Array();

    const useConsole = false;
    protected $console;

    public function __construct(){

        $Class = get_called_class();

        if(file_exists($Class::SRC)){
            Alien::getInstance()->getConsole()->putMessage('Using <i>'.get_called_class().'</i>.');
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            die('AlienLayot::construct() - Bad SRC '.$Class::SRC);
        }

    }

    public static final function autoloader(){
        require_once 'layouts/IndexLayout.php';
        require_once 'layouts/LoginLayout.php';
    }

    public abstract function getPartials();
    public abstract function handleResponse(ActionResponse $response);

    public final function renderToString(){

        $Class = get_called_class();
        $view = new AlienView($Class::SRC, null);

        if($Class::useNotifications){
            $sessionNotifications = unserialize($_SESSION['notifications']);
            if(isset($_SESSION['notifications']) && sizeof($sessionNotifications)){
                $notifications = new AlienView('display/system/notifications.php', null);
                $notifications->List = $sessionNotifications;
                $this->flushNotifications();
                $notifications = $notifications->renderToString();
            } else {
                $notifications = '';
            }
            $view->Notifications = $notifications;
        }

        $partials =  $this->getPartials();
        if(sizeof($partials)){
            foreach($partials as $k => $v){
                $view->$k = $v;
            }
        }

        $content = $view->renderToString();

        if($Class::useConsole){
            if((true || Alien::getParameter('debugMode')) && Authorization::getCurrentUser()->getId()){
                $console = new AlienView('display/system/console.php', null);
                $console->Messages = Alien::getInstance()->getConsole()->getMessageList();
                $content .= $console->renderToString();
            }
        }

        return $content;
    }

    public function putNotificaion(Notification $notification){
        $this->notifications[] = $notification;
        $this->saveSessionNotifications();
    }

    private function flushNotifications(){
        unset($this->notifications, $_SESSION['notifications']);
        $this->notifications = Array();
    }

    public function saveSessionNotifications(){
        $_SESSION['notifications'] = serialize($this->notifications);
    }

}