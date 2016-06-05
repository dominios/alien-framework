<?php

use Alien\Di\ServiceLocatorInterface;

class ServiceLocatorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    public function setUp()
    {
        $this->serviceLocator = new \Alien\Di\ServiceLocator();
    }

    public function testBuildFromArray()
    {


        $service = [
            'MyService' => [
                'aliases' => [
                    'Service123', 'Service456'
                ],
                'factory' => function (ServiceLocatorInterface $serviceLocator) {
                    return new stdClass;
                },
                'shared' => false
            ]
        ];

        $configuration = \Alien\Di\Configuration::createFromArray($service);
        $sl = $this->serviceLocator;
        $this->assertEquals('MyService', $configuration->getName());
        $this->assertEquals(false, $configuration->isShared());
        $this->assertEquals(['Service123', 'Service456'], $configuration->getAliases());
        $sl->register($configuration);
        $this->assertInstanceOf('stdClass', $sl->get('MyService'));

    }

    public function testRegisterService()
    {
        $mockConfiguration = $this->getMock('ServiceConfigurationInterface');
        $mockConfiguration->method('getName')->willReturn('TestingService');
        $mockConfiguration->method('getAliases')->willReturn(['AAA', 'BBB']);
        $mockConfiguration->method('getFactory')->willReturn(new stdClass());
        $mockConfiguration->method('isShared')->willReturn(false);
        $mockConfiguration->method('getOption')->with('foo')->willReturn('bar');

        $this->serviceLocator->register($mockConfiguration, "TestingService");

        $test = $this->serviceLocator->get("TestingService");

        // $this->assertInstanceOf('stdClass', $test);

        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testRegisterObject()
    {
        $object = $this->getMock('stdClass');
        $this->serviceLocator->register($object, 'Service');
        $this->assertInstanceOf('stdClass', $this->serviceLocator->get('Service'));
    }

}