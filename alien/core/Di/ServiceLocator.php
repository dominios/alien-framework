<?php

namespace Alien\Di;

use Alien\ConfigurationInterface;
use Alien\Di\Exception\InvalidServiceException;
use Alien\Di\Exception\ServiceAlreadyExistsException;
use Alien\Di\Exception\ServiceNotFoundException;
use Closure;
use ReflectionClass;

/**
 * Register of known services and dependency injection container.
 */
class ServiceLocator implements ServiceLocatorInterface
{

    /**
     * Array of known factories.
     *
     * @var callable[]
     */
    private $factories = [];

    /**
     * Array of known service configurations.
     * @var ServiceConfigurationInterface[]
     */
    private $configurations = [];

    /**
     * Array of registered instances.
     *
     * @var object[]
     */
    private $sharedInstances = [];

    /**
     * ServiceLocator constructor.
     *
     * If application configuration is passed as argument, <code>ServiceLocator</code> tries to
     * fetch services factories from it. ServiceLocator searches by key <code>service</code>.
     *
     * @param ConfigurationInterface $configuration optional configuration to fetch factories from.
     */
    public function __construct(ConfigurationInterface $configuration = null)
    {
        if ($configuration !== null) {
            $this->factories = $configuration->get('services');
        }
    }

    /**
     * Returns instance of service by it's name.
     *
     * <code>ServiceLocator</code> searches in array of known and registered services.
     * If instance of requested service is not found, searching continues with
     * list of defined factories specified in provided configuration in constructor.
     *
     * Each service in configuration can be specified two ways:
     * <ol>
     *  <li><b>full qualified name</b> (<code>string</code>) of class to instantiate,</li>
     *  <li>or <b>via factory</b> (<code>callable function</code>) which is called and constructs new instance. Instance of <code>ServiceLocator</code> is passed to this function as only argument automatically.</li>
     * </ol>
     *
     * Each service which is successfully created is automatically registered so each next call returns same instance
     * (it is some kind of <i>Singleton design pattern</i> simulation).
     *
     * @param string $name service name to get.
     * @throws ServiceNotFoundException when service is not registered and cannot be instantiated.
     * @return object
     * @todo rewrite this doc after issue #19 implementation
     */
    public function get($name)
    {
        // instance to return
        $instance = null;

        // @todo fetch from configuration ?
        $fetchInstance = function ($name) {
            if (array_key_exists($name, $this->sharedInstances)) {
                return $this->sharedInstances[$name];
            } else {
                throw new ServiceNotFoundException(sprintf("Service %s is not registered.", $name));
            }
        };

        $newInstance = function (callable $factory) {
            return $factory($this);
        };

        $registerInstance = function ($instance, $name, array $aliases = null) {
            $this->sharedInstances[$name] = $instance;
            if (count($aliases)) {
                foreach ($aliases as $alias) {
                    $this->sharedInstances[$alias] = $instance;
                }
            }
        };

        if (array_key_exists($name, $this->configurations)) {
            $configuration = $this->configurations[$name];
            if ($configuration->isShared()) {
                try {
                    $instance = $fetchInstance($name);
                } catch (ServiceNotFoundException $e) {
                    $instance = $newInstance($configuration->getFactory());
                    $registerInstance($instance, $configuration->getName(), $configuration->getAliases());
                }
            } else {
                $instance = $newInstance($configuration->getFactory());
            }
        }

        // service was registered as object
        // @todo this should be also changed - when registering any class, configuration should be also created as well
        if (!is_object($instance) && array_key_exists($name, $this->sharedInstances)) {
            return $this->sharedInstances[$name];
        }

        // remove; each factory should be internally converted to configuration
        // then, this block will not be needed
        // @todo read above
        if (array_key_exists($name, $this->factories)) {
            $service = $this->factories[$name]($this);
            $this->register($service, $name);
            return $service;
        }

        if ($instance === null || !is_object($instance)) {
            throw new ServiceNotFoundException(sprintf("Service %s is not registered.", $name));
        }

        return $instance;

    }

    /**
     * Register object as service.
     *
     * Use this method to register any object as known service.
     * After registration, access to injected instance will be anywhere from application by calling
     * <code>ServiceLocator::get(<NAME>)</code>.
     *
     * To successfully register service, two conditions must be fulfilled:
     * <ol>
     *  <li>only objects can be registered (no scalar values or closures are accepted),</li>
     *  <li>there cannot be multiple services with same name defined.</li>
     * </ol>
     *
     * Providing service name second argument <code>$name</code> is optional.
     * Full qualified class name is used, when this argument is left blank.
     *
     * @param object $service instance to register
     * @param string $name name of service
     * @throws InvalidServiceException when first argument is not valid object
     * @throws ServiceAlreadyExistsException when if service is already registered.
     * @todo rewrite this doc after issue #19 implementation
     */
    public function register($service, $name = null)
    {

        if ($service instanceof ServiceConfigurationInterface) {
            $this->handleConfigurationInterface($service, $name);
            return;
        }

        // @todo when it is not configuration, create new one

        if (!is_object($service) || $service instanceof Closure) {
            throw new InvalidServiceException("Trying to register invalid object as a service.");
        }

        if (is_null($name)) {
            // @todo debug if reflection is needed
            $reflection = new ReflectionClass($service);
            $name = $reflection->getName();
        }

        if (array_key_exists($name, $this->sharedInstances)) {
            throw new ServiceAlreadyExistsException(sprintf("Service %s is already registered", $name));
        }

        $this->sharedInstances[$name] = $service;

    }

    /**
     * @param $object
     * @todo
     */
    protected function createConfiguration($object)
    {

    }

    /**
     * Saves service configuration for service creation.
     *
     * Configuration will be available by it's defined name and also by each of it's aliases.
     * If either one of this names has already been used, exception is thrown.
     *
     * It is also possible to provide <code>$name</code> for service, which will be used as it's name.
     * This name comes from <code>register()</code> method call.
     *
     * <b>NOTE:</b> When no name is passed, <code>getName()</code> is called upon service <code>$configuration</code>.
     *
     * @param ServiceConfigurationInterface $configuration .
     * @param string $name optional name for service
     * @throw ServiceAlreadyExistsException when name or alias has already been used.
     */
    protected function handleConfigurationInterface(ServiceConfigurationInterface $configuration, $name = null)
    {
        $checkName = function ($name) {
            if ($this->configurationExists($name)) {
                throw new ServiceAlreadyExistsException(sprintf("Cannot register service %s, name already taken.", $name));
            }
        };
        $name = $name ?: $configuration->getName();
        $checkName($name);
        $this->configurations[$name] = $configuration;
        if (count($configuration->getAliases())) {
            foreach ($configuration->getAliases() as $alias) {
                $checkName($alias);
                $this->configurations[$alias] = $configuration;
            }
        }
    }

    /**
     * Checks if configuration with given name already exists.
     * @param string $name needle
     * @return bool
     */
    protected function configurationExists($name)
    {
        return in_array($name, $this->configurations);
    }

    /**
     * @param $service
     * @todo
     */
    public function isRegistered($service)
    {

    }

}