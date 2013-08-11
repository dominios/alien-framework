<?php

class ActionResponse {

    const RESPONSE_OK = 'ok';
    const RESPONSE_ERR = 'error';

    private $status;
    private $data;
    private $action;

    public function __construct($status = self::RESPONSE_OK, $data = null, $action = ''){
        $this->status = $status;
        $this->data = $data;
        $this->action = $action;
    }

    public function getStatus(){
        return $this->status;
    }

    public function getData(){
        return $this->data;
    }

    public function getAction(){
        return $this->action;
    }
}