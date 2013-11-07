<?php

namespace Alien;

class Response {

    const OK = 'ok';
    const ERROR = 'error';

    private $status;
    private $data;
    private $action;

    public function __construct($status = self::OK, $data = null, $action = '') {
        $this->status = $status;
        $this->data = $data;
        $this->action = $action;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getData() {
        return $this->data;
    }

    public function getAction() {
        return $this->action;
    }

}

