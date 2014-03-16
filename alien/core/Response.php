<?php

namespace Alien;

class Response {

    const HTTP_SUCCESS = 200;
    const HTTP_NOT_FOUND = 404;
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    private $status;
    private $data;
    private $action;

    public function __construct($data = null, $status = self::HTTP_SUCCESS, $action = '') {
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

