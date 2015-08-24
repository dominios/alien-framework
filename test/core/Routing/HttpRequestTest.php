<?php

require_once __DIR__ . '/../../../alien/core/Routing/RequestInterface.php';
require_once __DIR__ . '/../../../alien/core/Routing/HttpRequest.php';
require_once __DIR__ . '/../../../alien/core/Routing/Exception/InvalidHttpRequestException.php';

use Alien\Routing\HttpRequest;

class HttpRequestTest extends PHPUnit_Framework_TestCase {

    public function setUp() {

    }

    public function testValidCreateFromString() {

        $validRequestLines = [
            'GET http://www.example.com/index.html HTTP/1.0',
            'HEAD http://www.example.com/index.html HTTP/1.0',
            'POST http://www.example.com/index.html HTTP/1.0',
            'PUT http://www.example.com/index.html HTTP/1.0',
            'DELETE http://www.example.com/index.html HTTP/1.0',
            'CONNECT http://www.example.com/index.html HTTP/1.0',
            'OPTIONS http://www.example.com/index.html HTTP/1.0',
            'TRACE http://www.example.com/index.html HTTP/1.0',
            'GET http://www.example.com/index.html HTTP/1.1',
            'HEAD http://www.example.com/index.html HTTP/1.1',
            'POST http://www.example.com/index.html HTTP/1.1',
            'PUT http://www.example.com/index.html HTTP/1.1',
            'DELETE http://www.example.com/index.html HTTP/1.1',
            'CONNECT http://www.example.com/index.html HTTP/1.1',
            'OPTIONS http://www.example.com/index.html HTTP/1.1',
            'TRACE http://www.example.com/index.html HTTP/1.1',
        ];

        foreach($validRequestLines as $r) {
            $this->assertInstanceOf('Alien\Routing\HttpRequest', HttpRequest::createFromString($r));
        }

    }

    /**
     * @expectedException \Alien\Routing\Exception\InvalidHttpRequestException
     */
    public function testInvalidMethodException() {
        $this->assertEmpty(HttpRequest::createFromString('GETX http://www.example.com/index.html HTTP/1.0'));
    }

    /**
     * @expectedException \Alien\Routing\Exception\InvalidHttpRequestException
     */
    public function testInvalidVersionException() {
        $this->assertEmpty(HttpRequest::createFromString('GET http://www.example.com/index.html HTTP/1.2'));
    }

    /**
     * @expectedException \Alien\Routing\Exception\InvalidHttpRequestException
     */
    public function testInvalidUriException() {
        $this->assertEmpty(HttpRequest::createFromString('GET    HTTP/1.2'));
    }
}