<?php

namespace Application;

use Alien\Routing\Router;

class Application extends \Alien\Application {

    /**
     * @var Router
     */
    protected $router;

    public function run() {

        $this->router = $this->getServiceLocator()->getService('Router');

        echo "OK";

    }

}