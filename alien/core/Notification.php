<?php

namespace Alien;

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
    public function __construct($msg, $type) {
        $this->message = $msg;
        $this->type = $type;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getType() {
        return $this->type;
    }

}

?>
