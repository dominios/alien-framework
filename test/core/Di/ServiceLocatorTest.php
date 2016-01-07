<?php

use Alien\Di\ServiceConfigurationInterface;

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
        return function (\Alien\Di\ServiceLocatorInterface $serviceLocator) {
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
     * @var \Alien\Di\ServiceLocatorInterface
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

}