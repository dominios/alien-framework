<?php

namespace Alien\Routing;

/**
 * Route object
 * @package Alien\Routing
 */
class Route implements RouteInterface
{

    /**
     * Controller class name
     * @var string
     */
    protected $controllerClass;

    /**
     * Action name to call
     * @var string
     */
    protected $action;

    /**
     * Route path
     * @var string
     */
    protected $route;

    /**
     * Route query parameters
     * @var array
     */
    protected $params;

    /**
     * @param $controllerClass string
     * @param $action string
     * @param $route string
     * @param $params array
     */
    function __construct($controllerClass, $action, $route, $params = [])
    {
        $this->controllerClass = $controllerClass;
        $this->action = $action;
        $this->route = $route;
        $this->params = $params;
    }

    /**
     * Returns controller class name
     * @return string
     */
    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * Returns action name to call
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Returns route path
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Returns route parameters
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

}