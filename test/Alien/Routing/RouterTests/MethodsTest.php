<?php

use Alien\Routing\HttpRequest;
use Alien\Routing\Router;

class RouterHttpMethodsConstraintTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Router
     */
    private $router;

    private $expected;

    public function setUp() {
        $routes = [
            'http' => [
                // example request: GET /http/ HTTP/1.1
                // executes listAction
                'route' => 'http',
                'controller' => 'HttpController',
                'action' => 'list',
                'childRoutes' => [
                    '' => [
                        // example request: GET /http/1 HTTP/1.1
                        // executes getAction
                        'route' => '/:id',
                        'method' => 'GET',
                        'action' => 'get',
                        // once method specified, only those defined can be triggered
                        // GET, POST, PATCH & DELETE are now valid ones
                        // any other request method should throw exception
                        'overrides' => [
                            [
                                // example request #1: POST /http/1 HTTP/1.1
                                // example request #2: PATCH /http/1 HTTP/1.1
                                // both will execute patchAction
                                'method' => ['POST', 'PATCH'],
                                'action' => 'patch'
                            ],
                            [
                                // example request: DELETE /http/1 HTTP/1.1
                                // executes deleteAction
                                'method' => 'DELETE',
                                'action' => 'delete'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $this->router = new Router($routes);
        $this->expected = [
            'route' => 'http',
            'controller' => 'HttpController',
            'action' => 'list',
            'namespace' => null,
            'params' => [],
            'defaults' => []
        ];
    }

    public function testShouldHandleStandardUrl() {
        $testRoute = '/http';
        $result = $this->router->getMatchedConfiguration($testRoute);
        $this->assertEquals($this->expected, $result);
    }

    public function testShouldHandleStandardHttpRequest () {
        $request = new HttpRequest('/http', HttpRequest::METHOD_GET);
        $result = $this->router->getMatchedConfiguration($request);
        $this->assertEquals($this->expected, $result);
    }

    public function testShouldNotMatchWhenDifferentMethodGiven () {
        // write data provider for methods
        $request = new HttpRequest('/http', HttpRequest::METHOD_PUT);
        $result = $this->router->getMatchedConfiguration($request);
        // use exception instead of false
        $this->assertEquals(false, $result);
    }

    public function testShouldHandleChildGetRouteRequest () {
        $request = new HttpRequest('/http/25', HttpRequest::METHOD_GET);
        $expected = array_merge($this->expected, ['action' => 'get']);
        $result = $this->router->getMatchedConfiguration($request);
        $this->assertEquals($expected, $result);
    }

    public function testShouldHandleChildPatchRequest () {
        $request = new HttpRequest('/http/25', HttpRequest::METHOD_PATCH);
        $expected = array_merge($this->expected, ['action' => 'patch']);
        $result = $this->router->getMatchedConfiguration($request);
        $this->assertEquals($expected, $result);
    }

    public function testShouldHandleChildPostRequest () {
        $request = new HttpRequest('/http/25', HttpRequest::METHOD_POST);
        $expected = array_merge($this->expected, ['action' => 'patch']);
        $result = $this->router->getMatchedConfiguration($request);
        $this->assertEquals($expected, $result);
    }

    public function testShouldHandleChildDeleteRequest () {
        $request = new HttpRequest('/http/25', HttpRequest::METHOD_DELETE);
        $expected = array_merge($this->expected, ['action' => 'delete']);
        $result = $this->router->getMatchedConfiguration($request);
        $this->assertEquals($expected, $result);
    }

    public function testShouldThrowExceptionWhenNoMethodMatch () {
        $request = new HttpRequest('/http/25', HttpRequest::METHOD_CONNECT);
        $result = $this->router->getMatchedConfiguration($request);
        $this->assertEquals(false, $result);
    }

}