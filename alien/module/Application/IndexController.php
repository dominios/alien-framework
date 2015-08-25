<?php

namespace Application;

use Alien\Mvc\AbstractController;

class IndexController extends AbstractController {

    protected function homeAction() {
        $this->getResponse()->setContentType('text/html;charset=UTF8');
        $this->getResponse()->setContent('<h1>Hello World!</h1>');
    }

}