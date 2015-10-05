<?php

use Alien\Routing\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{

    public function testCreateFromConfigurationFactory()
    {
        $configuration = [
            'route' => '/example',
            'namespace' => 'Example\Namespace',
            'controller' => 'RouteController',
            'action' => 'testAction'
        ];

        $route = Route::createFromRouteConfiguration($configuration);
        $this->assertInstanceOf('\Alien\Routing\RouteInterface', $route);
        $this->assertEquals('/example', $route->getRoute());
        $this->assertEquals('Example\Namespace\RouteController', $route->getControllerClass());
        $this->assertEquals('testAction', $route->getAction());
        $this->assertEquals([], $route->getParams());
    }
}