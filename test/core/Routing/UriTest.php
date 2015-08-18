<?php

require_once __DIR__ . '/../../../alien/core/Routing/Uri.php';

class UriTest extends PHPUnit_Framework_TestCase {

    public function testValidCreationFromString() {
        $uri = "foo://username:password@example.com:8042/over/there/index.dtb?type=animal&name=narwhal#&nose";
        $test = \Alien\Routing\Uri::createFromString($uri);
        $this->assertInstanceOf('\Alien\Routing\Uri', $test);
    }

}