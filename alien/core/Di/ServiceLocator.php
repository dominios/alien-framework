<?php

namespace Alien\Di;

use Alien\Configuration;
use Alien\Di\Exception\InvalidServiceException;
use Alien\Di\Exception\ServiceAlreadyExistsException;
use Alien\Di\Exception\ServiceNotFoundException;
use Closure;
use ReflectionClass;

/**
 * Register of known services and dependency injection container
 *
 * @package Alien\Di
 */
class ServiceLocator implements ServiceLocatorInterface
{

    /**
     * Array of known factories
     * @var array
     */
    private $factories = [];

    /**
     * Array of registered instances
     * @var array
     */
    private $instances = [];

    public function __construct(Configuration $configuration)
    {
        $this->factories = $configuration->get('services');
    }

    /**
     * Returns instance of service by it's name
     *
     * <i>ServiceLocator</i> searches in array of known and registered services.
     * If instance of requested service is not found, searching continues with
     * list of defined factories specified in provided configuration in constructor.
     *
     * Each service in configuration can be specified two ways:
     * <ol>
     *  <li><b>full qualified name</b> (<code>string</code>) of class to instantiate,</li>
     *  <li>or <b>via factory</b> (<code>callable function</code>) which is called and constructs new instance. Instance of <code>ServiceLocator</code> is passed to this function as only argument automatically.</li>
     * </ol>
     *
     * Each service that is successfully created is automatically registered so each next call returns same instance
     * (it is some kind of <i>Singleton design pattern</i> simulation).
     *
     * @param string $name service name
     * @throws ServiceNotFoundException when service is not registered and cannot be instantiated
     * @return object
     * @todo factory v configu zmenit na pole, kde bude options (povolit len 1 instanciu?...) a samotna factory
     * @todo aliasy pre services
     */
    public function getService($name)
    {

        if (array_key_exists($name, $this->instances)) {
            return $this->instances[$name];
        }

        if (array_key_exists($name, $this->factories)) {
            $service = $this->factories[$name]($this);
            $this->registerService($service, $name);
            return $service;
        }

        throw new ServiceNotFoundException("Service $name is not registered");

    }

    /**
     * Register object as service
     *
     * Use this method to register any object as known service.
     * After registration, access to injected instance will be anywhere from application by calling
     * <code>ServiceLocator::getService(<NAME>)</code>.
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
     */
    public function registerService($service, $name = null)
    {

        if (!is_object($service) || $service instanceof Closure) {
            throw new InvalidServiceException("Trying to register invalid object as a service");
        }

        if (is_null($name)) {
            $reflection = new ReflectionClass($service);
            $name = $reflection->getName();
        }

        if (array_key_exists($name, $this->instances)) {
            throw new ServiceAlreadyExistsException("Service $name is already registered");
        }

        $this->instances[$name] = $service;

    }

}