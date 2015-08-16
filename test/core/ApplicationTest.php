<?php

require_once __DIR__ . '/../../alien/core/Configuration.php';
require_once __DIR__ . '/../../alien/core/Application.php';
require_once __DIR__ . '/../../alien/core/Di/ServiceLocatorInterface.php';
require_once __DIR__ . '/../../alien/core/Di/ServiceLocator.php';

use Alien\Application;

class ApplicationTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Application
     */
    private $application;

    public function setUp() {
        $this->application = new \Alien\Application();
    }

    public function testBootstrap() {
//        $conf = $this->getMock('\Alien\Configuration');
        $conf = $this->getMock('Alien\Configuration');
        $this->assertEmpty($this->application->bootstrap($conf), "Bootstrap process cannot return anything!");
    }

}
