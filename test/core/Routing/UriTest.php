<?php

use Alien\Routing\Uri;

require_once __DIR__ . '/../../../alien/core/Routing/Uri.php';

class UriTest extends PHPUnit_Framework_TestCase {

    private $correctFullUrl;

    public function setUp() {
        $this->correctFullUrl = "foo://username:password@example.com:8042/over/there/index.dtb?type=animal&name=narwhal#&nose";
    }

    public function testValidCreationFromString() {
        $test = Uri::createFromString($this->correctFullUrl);
        $this->assertInstanceOf('\Alien\Routing\Uri', $test);
    }

    public function testGetProtocol() {
        $uri = Uri::createFromString($this->correctFullUrl);
        $this->assertEquals('foo', $uri->getProtocol());
    }

    public function testGetUsername() {
        $uri = Uri::createFromString($this->correctFullUrl);
        $this->assertEquals('username', $uri->getUsername());
    }

    public function testGetPassword() {
        $uri = Uri::createFromString($this->correctFullUrl);
        $this->assertEquals('password', $uri->getPassword());
    }

    public function testGetHost() {
        $uri = Uri::createFromString($this->correctFullUrl);
        $this->assertEquals('example.com', $uri->getHost());
    }

    public function testGetDomains() {
        $uri = Uri::createFromString($this->correctFullUrl);
        $domains = ['example', 'com'];
        $this->assertSame($domains, $uri->getDomains());
    }

    public function testGetPort() {
        $uri = Uri::createFromString($this->correctFullUrl);
        $this->assertEquals('8042', $uri->getPort());
    }

    public function testGetPath() {
        $uri = Uri::createFromString($this->correctFullUrl);
        $this->assertEquals('/over/there/index.dtb', $uri->getPath());
    }

    public function testGetQuery() {
        $uri = Uri::createFromString($this->correctFullUrl);
        $this->assertEquals('type=animal&name=narwhal', $uri->getQuery());
    }

    public function testGetParams() {
        $uri = Uri::createFromString($this->correctFullUrl);
        $params = [
            'type' => 'animal',
            'name' => 'narwhal'
        ];
        $this->assertSame($params, $uri->getParams(), 'Failed to get params when multiple params in URI');

        $uri2 = Uri::createFromString('example.com?x=y', 'Failed to get params when only one pair of params in URI');
        $this->assertSame(['x' => 'y'], $uri2->getParams());

        $uri3 = Uri::createFromString('example.com', 'Failed to get params when no params in URI');
        $this->assertSame([], $uri3->getParams());

    }

    public function testGetFragment() {
        $uri = Uri::createFromString($this->correctFullUrl);
        $this->assertEquals('&nose', $uri->getFragment());
    }

    public function testRelativeUrl() {
        $uri = Uri::createFromString("/www/example/www");
        $this->assertEquals('/www/example/www', $uri->getPath());
        $this->assertEmpty($uri->getProtocol());
        $this->assertEmpty($uri->getUsername());
        $this->assertEmpty($uri->getPassword());
        $this->assertEmpty($uri->getHost());
        $this->assertEmpty($uri->getDomains());
        $this->assertEmpty($uri->getQuery());
        $this->assertEmpty($uri->getParams());
        $this->assertEmpty($uri->getFragment());
    }

}