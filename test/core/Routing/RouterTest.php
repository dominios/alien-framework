<?php

use Alien\Routing\Router;

require_once __DIR__ . '/../../../alien/core/Routing/Router.php';
require_once __DIR__ . '/../../../alien/core/Routing/Exception/RouteNotFoundException.php';
require_once __DIR__ . '/../../../alien/core/Routing/Exception/InvalidConfigurationException.php';

class RouterTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Router
     */
    private $router;

    public function setUp() {
        $routes = [
            '' => [
                'route' => '/',
                'controller' => 'ExampleController',
                'namespace' => 'Example\Namespace',
                'action' => 'index'
            ],
            'test' => [
                'route' => '/test',
                'controller' => 'ExampleController',
                'namespace' => 'Example\Namespace',
                'action' => 'index',
                'childRoutes' => [
                    'child1' => [
                        'route' => '/child1',
                        'action' => 'child1'
                    ],
                    'child2' => [
                        'route' => '/child2',
                        'controller' => 'ChildController',
                        'namespace' => 'Child\Namespace',
                        'action' => 'child2'
                    ]
                ]
            ]
        ];
        $this->router = new Router($routes);
    }

    /**
     * @expectedException \Alien\Routing\Exception\RouteNotFoundException
     */
    public function testNoMatch() {
        $testRoute = 'notExistingRoute';
        $this->assertEmpty($this->router->getMatchedConfiguration($testRoute));
    }

    /**
     * @expectedException \Alien\Routing\Exception\RouteNotFoundException
     */
    public function testRouteNotFound() {
        $testRoute = 'notExistingRoute';
        $this->assertEmpty($this->router->getRoute($testRoute));
    }

    public function testSimpleRouteNoParams() {
        $testRoute = '/test';
        $expectedResult = [
            'route' => '/test',
            'controller' => 'ExampleController',
            'namespace' => 'Example\Namespace',
            'action' => 'index',
            'params' => []
        ];
        $result = $this->router->getMatchedConfiguration($testRoute);
        $this->assertEquals($expectedResult, $result);
    }

    public function testChildRouteNoParams() {
        $testRoute = '/test/child1';
        $expectedResult = [
            'route' => '/test/child1',
            'controller' => 'ExampleController',
            'namespace' => 'Example\Namespace',
            'action' => 'child1',
            'params' => []
        ];
        $result = $this->router->getMatchedConfiguration($testRoute);
        $this->assertEquals($expectedResult, $result);

        $testRoute = '/test/child2';
        $expectedResult = [
            'route' => '/test/child2',
            'controller' => 'ChildController',
            'namespace' => 'Child\Namespace',
            'action' => 'child2',
            'params' => []
        ];
        $result = $this->router->getMatchedConfiguration($testRoute);
        $this->assertEquals($expectedResult, $result);
    }

    public function testEmptyRoute() {
        $testRoute = '';
        $expectedResult = [
            'route' => '/',
            'controller' => 'ExampleController',
            'namespace' => 'Example\Namespace',
            'action' => 'index',
            'params' => []
        ];
        $result = $this->router->getMatchedConfiguration($testRoute);
        $this->assertEquals($expectedResult, $result);
    }

    public function testEmptyAndSingleSlashEquality() {
        $empty = '';
        $singleSlash = '/';
        $this->assertSame($this->router->getMatchedConfiguration($empty), $this->router->getMatchedConfiguration($singleSlash));
    }

}