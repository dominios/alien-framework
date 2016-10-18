<?php

use Alien\Routing\HttpRequest;
use Alien\Routing\RouteMatch;
use Alien\Routing\Router;

/**
 * Class RouterHttpMethodsConstraintTest
 * @group Routing
 */
class HttpRouter extends PHPUnit_Framework_TestCase
{

    /**
     * @var Router
     */
    private $router;

    private $expected;

    public function setUp()
    {
        $routes = [
            'http' => [
                // example request: GET /http/ HTTP/1.1
                // executes listAction
                'route' => '/http',
                'controller' => 'HttpController',
                'action' => 'list',
                'child_routes' => [
                    '' => [
                        // example request: GET /http/1 HTTP/1.1
                        // executes getAction
                        'route' => '/:id',
                        'http_method' => 'GET',
                        'action' => 'get',
                        'constraints' => [
                            'id' => [
                                'type' => '\Alien\Constraint\Range',
                                'min' => 1
                            ]
                        ],
                        // once method specified, only those defined can be triggered
                        // GET, POST, PATCH & DELETE are now valid ones
                        // any other request method should throw exception
                        'options' => [
                            [
                                // example request #1: POST /http/1 HTTP/1.1
                                // example request #2: PATCH /http/1 HTTP/1.1
                                // both will execute patchAction
                                'http_method' => ['POST', 'PATCH'],
                                'action' => 'patch'
                            ],
                            [
                                // example request: DELETE /http/1 HTTP/1.1
                                // executes deleteAction
                                'http_method' => 'DELETE',
                                'action' => 'delete'
                            ]
                        ]
                    ]
                ]
            ],
            'segments' => [
                'route' => '/any',
                'controller' => 'AnyLevelController',
                'action' => 'root',
                'child_routes' => [
                    'level' => [
                        'route' => '/level',
                        'action' => 'whatever',
                        'child_routes' => [
                            'level2a' => [
                                'route' => '/level2a',
                                'action' => 'whateverElseA'
                            ],
                            'level2b' => [
                                'route' => '/level2b',
                                'action' => 'whateverElseB',
                                'child_routes' => [
                                    'level3' => [
                                        'route' => '/level3',
                                        'action' => 'deepLevel'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'arguments' => [
                'route' => '/arg',
                'controller' => 'ArgumentsController',
                'action' => 'handle',
                'child_routes' => [
                    'single-required' => [
                        'route' => '/s/:foo'
                    ],
                    'multiple-required' => [
                        'route' => '/m/:foo/:bar/:baz'
                    ],
                    'multiple-separated-by-path' => [
                        'route' => '/p/:foo/path/:baz'
                    ],
                    'single-optional' => [
                        'route' => '/x[/:foo]'
                    ],
                    'multiple-optional' => [
                        'route' => '/x[/:foo][/:bar][/:baz]'
                    ]
                ]
            ]
        ];
        $this->router = new Router($routes);
        $this->expected = [
            'route' => '/http',
            'controller' => 'HttpController',
            'action' => 'list',
            'params' => [],
            'defaults' => []
        ];
    }

    /**
     * @test
     * @testdox function `getMatchedConfiguration` should handle standard URL passed as string.
     */
    public function handleStandardUrl()
    {
        $testRoute = '/http';
        $result = $this->router->getMatchedConfiguration($testRoute);
        $this->assertEquals($this->expected, $result);
    }

    /**
     * @test
     * @testdox function `getMatchedConfiguration` should handle HttpRequest object.
     */
    public function handleHttpRequest()
    {
        $request = new HttpRequest();
        $request->setMethod(HttpRequest::METHOD_GET)->setUri('/http');
        $result = $this->router->getMatchedConfiguration($request);
        $this->assertEquals($this->expected, $result);
    }

    /**
     * @test
     * @testdox function `match` should accept HttpObject instance or string.
     */
    public function matchShouldAcceptHttpRequestOrString()
    {
        $this->assertInstanceOf('\Alien\Routing\RouteMatch', $this->router->match('/http'));
        $this->assertInstanceOf('\Alien\Routing\RouteMatch', $this->router->match(HttpRequest::createFromString('GET /http HTTP/1.1')));
    }

    /**
     * @test
     * @testdox function `match` should throw an exception when other other supported argument given.
     * @expectedException InvalidArgumentException
     * @todo data provider for more options
     */
    public function matchShouldNotAcceptOtherArguments()
    {
        $this->assertNull($this->router->match(true));
    }

    /**
     * @test
     * @testdox function `match` should search recursively for match.
     * @dataProvider anyLevelDataProvider
     */
    public function matchShouldFindMatchAtAnyLevel($expected, $route)
    {
        $this->assertEquals($expected, $this->router->match($route));;
    }

    public function anyLevelDataProvider ()
    {
        return [
            'first level' => [ new RouteMatch('/any', 'root', 'AnyLevelController'), '/any' ],
            'second level' => [ new RouteMatch('/any/level', 'whatever', 'AnyLevelController'), '/any/level' ],
            'third level a' => [ new RouteMatch('/any/level/level2a', 'whateverElseA', 'AnyLevelController'), '/any/level/level2a' ],
            'third level b' => [ new RouteMatch('/any/level/level2b', 'whateverElseB', 'AnyLevelController'), '/any/level/level2b' ],
            'fourth level' => [ new RouteMatch('/any/level/level2b/level3', 'deepLevel', 'AnyLevelController'), '/any/level/level2b/level3' ]
        ];
    }

    /**
     * @testdox function `match` should handle routes with required arguments
     * @dataProvider parametrizedDataProvider
     * @group current
     */
    public function matchShouldHandleRequiredArguments ($expected, $route)
    {
        $this->assertEquals($expected, $this->router->match($route));;
    }

    public function parametrizedDataProvider ()
    {
        return [
            'single required' => [ new RouteMatch('/arg/s/:foo', 'handle', 'ArgumentsController', [ 'foo' => 123 ]), '/arg/s/123' ],
            'multiple required' => [ new RouteMatch('/arg/m/:foo/:bar/:baz', 'handle', 'ArgumentsController', [ 'foo' => 'x', 'bar' => 'y', 'baz' => 'z' ]), '/arg/m/x/y/z' ],
        ];
    }





    public function testShouldNotMatchWhenDifferentMethodGiven()
    {
        // write data provider for methods
        $request = new HttpRequest('/http', HttpRequest::METHOD_PUT);
        $result = $this->router->getMatchedConfiguration($request);
        // use exception instead of false
        $this->assertEquals(false, $result);
    }

    public function testShouldHandleChildGetRouteRequest()
    {
        $request = new HttpRequest('/http/25', HttpRequest::METHOD_GET);
        $expected = array_merge($this->expected, ['action' => 'get']);
        $result = $this->router->getMatchedConfiguration($request);
        $this->assertEquals($expected, $result);
    }

    public function testShouldHandleChildPatchRequest()
    {
        $request = new HttpRequest('/http/25', HttpRequest::METHOD_PATCH);
        $expected = array_merge($this->expected, ['action' => 'patch']);
        $result = $this->router->getMatchedConfiguration($request);
        $this->assertEquals($expected, $result);
    }

    public function testShouldHandleChildPostRequest()
    {
        $request = new HttpRequest('/http/25', HttpRequest::METHOD_POST);
        $expected = array_merge($this->expected, ['action' => 'patch']);
        $result = $this->router->getMatchedConfiguration($request);
        $this->assertEquals($expected, $result);
    }

    public function testShouldHandleChildDeleteRequest()
    {
        $request = new HttpRequest('/http/25', HttpRequest::METHOD_DELETE);
        $expected = array_merge($this->expected, ['action' => 'delete']);
        $result = $this->router->getMatchedConfiguration($request);
        $this->assertEquals($expected, $result);
    }

    public function testShouldThrowExceptionWhenNoMethodMatch()
    {
        $request = new HttpRequest('/http/25', HttpRequest::METHOD_CONNECT);
        $result = $this->router->getMatchedConfiguration($request);
        $this->assertEquals(false, $result);
    }

}