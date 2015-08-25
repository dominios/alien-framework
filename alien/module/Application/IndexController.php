<?php

namespace Application;

use Alien\Mvc\AbstractController;

class IndexController extends AbstractController {

    protected function homeAction() {
        echo "TEST";
    }

}