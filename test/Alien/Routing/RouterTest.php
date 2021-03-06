<?php

use Alien\Routing\Router;

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
            ],
            'parametric' => [
                'route' => '/parametric/:foo',
                'controller' => 'ParametricController',
                'namespace' => 'Parametric\Namespace',
                'action' => 'doSomething',
            ],
            'twoParams' => [
                'route' => '/twoParams/:foo1/:foo2',
                'controller' => 'ParametricController',
                'namespace' => 'Parametric\Namespace',
                'action' => 'doSomething',
            ],
            'optional' => [
                'route' => '/optional[/:foo]',
                'controller' => 'OptionalController',
                'namespace' => 'Optional\Namespace',
                'action' => 'doSomething'
            ],
            'api' => [
                'route' => '/api',
                'namespace' => '',
                'controller' => '',
                'action' => '',
                'childRoutes' => [
                    'v1' => [
                        'route' => '/v1',
                        'childRoutes' => [
                            'model' => [
                                'route' => '/model/:method[/:id]',
                                'controller' => 'Rest\Controllers\ModelController',
                                'action' => 'methodAction'
                            ]
                        ]
                    ]
                ]
            ],
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
            'params' => [],
            'defaults' => []
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
            'params' => [],
            'defaults' => []
        ];
        $result = $this->router->getMatchedConfiguration($testRoute);
        $this->assertEquals($expectedResult, $result);

        $testRoute = '/test/child2';
        $expectedResult = [
            'route' => '/test/child2',
            'controller' => 'ChildController',
            'namespace' => 'Child\Namespace',
            'action' => 'child2',
            'params' => [],
            'defaults' => []
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
            'params' => [],
            'defaults' => []
        ];
        $result = $this->router->getMatchedConfiguration($testRoute);
        $this->assertEquals($expectedResult, $result);
    }

    public function testEmptyAndSingleSlashEquality() {
        $empty = '';
        $singleSlash = '/';
        $this->assertSame($this->router->getMatchedConfiguration($empty), $this->router->getMatchedConfiguration($singleSlash));
    }

    public function testSimpleRouteWithMandatoryParams() {
        $testRoute1 = 'parametric/bar';
        $expectedResult = [
            'route' => '/parametric/:foo',
            'controller' => 'ParametricController',
            'namespace' => 'Parametric\Namespace',
            'action' => 'doSomething',
            'params' => [
                'foo' => 'bar'
            ],
            'defaults' => []
        ];
        $this->assertEquals($expectedResult, $this->router->getMatchedConfiguration($testRoute1));

        $testRoute2 = 'twoParams/bar1/bar2';
        $expectedResult = [
            'route' => '/twoParams/:foo1/:foo2',
            'controller' => 'ParametricController',
            'namespace' => 'Parametric\Namespace',
            'action' => 'doSomething',
            'params' => [
                'foo1' => 'bar1',
                'foo2' => 'bar2'
            ],
            'defaults' => []
        ];
        $this->assertEquals($expectedResult, $this->router->getMatchedConfiguration($testRoute2));
    }

    public function testSimpleRouteWithOptionalParams() {
        $testRoute1 = 'optional/bar';
        $expectedResult1 = [
            'route' => '/optional[/:foo]',
            'controller' => 'OptionalController',
            'namespace' => 'Optional\Namespace',
            'action' => 'doSomething',
            'params' => [
                'foo' => 'bar'
            ],
            'defaults' => []
        ];
        $this->assertEquals($expectedResult1, $this->router->getMatchedConfiguration($testRoute1));

        $testRoute2 = 'optional';
        $expectedResult2 = [
            'route' => '/optional[/:foo]',
            'controller' => 'OptionalController',
            'namespace' => 'Optional\Namespace',
            'action' => 'doSomething',
            'params' => [
                'foo' => null
            ],
            'defaults' => []
        ];
        $this->assertEquals($expectedResult2, $this->router->getMatchedConfiguration($testRoute2));
    }

    public function testApiSubRoute() {
        $testRouteGetList = '/api/v1/model/getList';
        $testRouteFindOne = '/api/v1/model/findOne/123';
        $expectedResultGetList = [
            'route' => '/api/v1/model/:method[/:id]',
            'namespace' => '',
            'controller' => 'Rest\Controllers\ModelController',
            'action' => 'methodAction',
            'params' => [
                'method' => 'getList',
                'id' => null
            ],
            'defaults' => []
        ];
        $this->assertEquals($expectedResultGetList, $this->router->getMatchedConfiguration($testRouteGetList));

        $expectedResultFindOne = [
            'route' => '/api/v1/model/:method[/:id]',
            'namespace' => '',
            'controller' => 'Rest\Controllers\ModelController',
            'action' => 'methodAction',
            'params' => [
                'method' => 'findOne',
                'id' => 123
            ],
            'defaults' => []
        ];
        $this->assertEquals($expectedResultFindOne, $this->router->getMatchedConfiguration($testRouteFindOne));
    }

}