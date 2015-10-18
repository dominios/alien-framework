<?php

namespace Application;

use Alien\Mvc\AbstractController;
use Alien\Mvc\Response;
use Alien\Routing\Route;
use Alien\Routing\Router;
use Alien\Routing\Uri;

class Application extends \Alien\Application
{

    /**
     * @var Router
     */
    protected $router;

    public function run()
    {

        $this->router = $this->getServiceLocator()->getService('Router');

        $uri = Uri::createFromString($_SERVER['REQUEST_URI']);
        $route = Route::createFromRouteConfiguration($this->router->getMatchedConfiguration($uri->getPath()));

        $controllerClass = $route->getControllerClass();
        /* @var $controller AbstractController */
        $controller = new $controllerClass;
        $controller->setServiceLocator($this->getServiceLocator());
        $controller->setRoute($route);
        $controller->addAction($route->getAction());
        /* @var $response Response */
        $response = $controller->getResponses()[0];

        header('Content-Type: ' . $response->getContentType());
        $content = $response->getContent();
        if(strpos($response->getContentType(), 'json') !== false) {
            $content = json_encode($content);
        }
        echo $content;


    }

}