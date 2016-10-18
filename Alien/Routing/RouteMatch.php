<?php

namespace Alien\Routing;

class RouteMatch
{

    /**
     * @var string
     */
    protected $route = "";

    /**
     * @var string
     */
    protected $action = "";

    /**
     * @var string
     */
    protected $controller = "";

    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     * RouteMatch constructor.
     * @param string $route
     * @param string $action
     * @param string $controller
     * @param array $params
     * @param array $defaults
     */
    public function __construct($route = null, $action = null, $controller = null, array $params = [], array $defaults = [])
    {
        $this->route = $route;
        $this->action = $action;
        $this->controller = $controller;
        $this->defaults = $defaults;
        $this->params = $params;
    }

    public function apply($array)
    {
        if (array_key_exists('route', $array)) {
            $this->appendUri($array['route']);
        }
        if (array_key_exists('action', $array)) {
            $this->setAction($array['action']);
        }
        if (array_key_exists('controller', $array)) {
            $this->setController($array['controller']);
        }
        if (array_key_exists('defaults', $array)) {
            $this->setDefaults($array['defaults']);
        }
        if (array_key_exists('params', $array)) {
            $this->setParams($array['params']);
        }
    }

    public function appendUri($uri)
    {
        $this->route .= $uri;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param array $defaults
     */
    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

}