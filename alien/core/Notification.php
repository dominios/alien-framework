<?php

namespace Alien;

class NotificationContainer {

    const sessionAutoUpdate = true;

    protected $notifications = array();
    private static $instance = null;

    private function __construct() {
        if (isset($_SESSION['notifications'])) {
            $this->notifications = unserialize($_SESSION['notifications']);
        }
    }

    private function __clone() {

    }

    /**
     *
     * @return \Alien\NotificationContainer
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function putNotification(Notification $notification) {
        $this->notifications[] = $notification;
        if (self::sessionAutoUpdate) {
            $this->updateSession();
        }
        return $this;
    }

    public function flushNotifications() {
        unset($this->notifications);
        unset($_SESSION['notifications']);
        $this->notifications = array();
        return $this;
    }

    public function updateSession() {
        $_SESSION['notifications'] = serialize($this->notifications);
        return $this;
    }

    public function getNotifications() {
        return $this->notifications;
    }

}

class Notification {

    private $message;
    private $type;

    const INFO = 'note';
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const ERROR = 'error';

    /**
     * New notification constructor
     * @param string $msg message to display
     * @param string $type type of message, one of the following: <b>note</b>, <b>success</b>, <b>warning</b> or <b>error</b>
     */
    private function __construct($msg, $type) {
        $this->message = $msg;
        $this->type = $type;
        NotificationContainer::getInstance()->putNotification($this);
    }

    public static function information($msg) {
        return new self($msg, Notification::INFO);
    }

    public static function success($msg) {
        return new self($msg, Notification::SUCCESS);
    }

    public static function warning($msg) {
        return new self($msg, Notification::WARNING);
    }

    public static function error($msg) {
        return new self($msg, Notification::ERROR);
    }

    public function getMessage() {
        return $this->message;
    }

    public function getType() {
        return $this->type;
    }

}

?>
