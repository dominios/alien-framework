<?php
namespace Alien\Routing;

/**
 * RouteInterface
 * @package Alien\Routing
 */
interface RouteInterface
{
    /**
     * Returns controller class name
     * @return string
     */
    public function getControllerClass();

    /**
     * Returns action name to call
     * @return string
     */
    public function getAction();

    /**
     * Returns route path
     * @return string
     */
    public function getRoute();

    /**
     * Returns route parameters
     * @return array
     */
    public function getParams();

}