<?php

use Alien\Di\ServiceConfigurationInterface;
use Alien\Di\ServiceLocatorInterface;

/**
 * @todo delete this class, mock instead
 */
class SingletonExampleService
{
    public function doSomething()
    {
        return true;
    }
}

/**
 * @todo delete this class, mock instead
 */
class SingletonExampleServiceConfiguration implements ServiceConfigurationInterface
{

    protected $options = [
        'foo' => 'bar'
    ];

    public function getName()
    {
        return "SingletonExampleService";
    }

    public function getAliases()
    {
        return ['SingletonAbc', 'SingletonDef'];
    }

    public function getFactory()
    {
        return function (ServiceLocatorInterface $serviceLocator) {
            return new SingletonExampleService();
        };
    }

    public function isShared()
    {
        return true;
    }

    public function getOption($key)
    {
        return $this->options[$key];
    }
}

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

    public function testRegisterService()
    {
        $configuration = new SingletonExampleServiceConfiguration();
        $this->serviceLocator->register($configuration, "RegisteredSingleton");

        $test = $this->serviceLocator->get("RegisteredSingleton");

        $this->assertInstanceOf('SingletonExampleService', $test);
    }

    public function testBuildFromArray()
    {
        $service = [
            'MyService' => [
                'aliases' => [
                    'Service123', 'Service456'
                ],
                'factory' => function (ServiceLocatorInterface $serviceLocator) {
                    return new SingletonExampleService();
                },
                'shared' => false
            ]
        ];

        $configuration = \Alien\Di\ServiceConfiguration::createFromArray($service);
        $sl = $this->serviceLocator;
        $this->assertEquals('MyService', $configuration->getName());
        $this->assertEquals(false, $configuration->isShared());
        $this->assertEquals(['Service123', 'Service456'], $configuration->getAliases());
        $sl->register($configuration);
        $this->assertInstanceOf('SingletonExampleService', $sl->get('MyService'));

    }

}