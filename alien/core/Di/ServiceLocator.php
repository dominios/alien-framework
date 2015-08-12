<?php

namespace Alien\Di;

use Alien\Configuration;
use Alien\Db\CRUDDao;
use Alien\Di\Exception\InvalidServiceException;
use Alien\Di\Exception\ServiceAlreadyExistsException;
use Alien\Di\Exception\ServiceNotFoundException;
use ReflectionClass;

class ServiceLocator implements ServiceLocatorInterface {

    /**
     * @var ServiceLocator
     */
    private static $instance = null;

    /**
     * @var array
     */
    private $services = array();

    /** @var array */
    private $factories = array();

    private function __construct() {
    }

    private function __clone() {
    }

    /**
     * Initialize the ServiceLocator
     *
     * @param array $config currently not used, but can change in future
     * @return ServiceLocator
     */
    public static function initialize(Configuration $config) {
        if (self::$instance === null) {
            self::$instance = new self;
            self::$instance->factories = $config->get('factories');
        }
        return self::$instance;
    }

    /**
     * Returns registered service if found, or throws exception on failure.
     * Searched name can be even namespaed name of service class or simple name. If there are more then one services with same
     * simple name, not available ServiceManagerException will be thrown.
     *
     * @param string $name service name
     * @throws ServiceManagerException
     * @return object
     */
    public function getService($name) {

        if(array_key_exists($name, $this->services)) {
            return $this->services[$name];
        }

        if(array_key_exists($name, $this->factories)) {
            $service = $this->factories[$name]($this);
            $this->registerService($service);
            return $service;
        }

        throw new ServiceNotFoundException("Service $name is not registered.");


        $test = strpos($name, '\\');
        if (!$test) {
            if (!array_key_exists($name, $this->services)) {
                throw new ServiceNotFoundException("Multiple or none services with name $name registered, namespace required.");
            }
        } elseif ($test != 0) {
            $name = '\\' . $name;
        }
        if (!array_key_exists($name, $this->services)) {
            throw new ServiceNotFoundException("Requested service $name is not available.");
        }
        return $this->services[$name];
    }

    /**
     * Gets DAO service by name. Due to added type-check, it's more IDE-friendly then simple ServiceLocator::getServce().
     * Searched name can be even namespaed name of DAO class or simple name. If there are more then one services with same
     * simple name, not available ServiceManagerException will be thrown.
     *
     * @param string $name service name
     * @throws ServiceManagerException
     * @return \Alien\Db\CRUDDaoImpl
     */
    public function getDao($name) {
        $dao = $this->getService($name);
        if ($dao instanceof CRUDDao) {
            return $dao;
        } else {
            throw new ServiceNotFoundException("No DAO service registered with name '$name'.");
        }
    }

    /**
     * Register any object as service. Any object can be registered only once.
     * Registered services may be called by ServiceLocator::getService() method using their full name (with namespace) or with simple name,
     * however registering two different services with same simple name leads to disable this feature for such classes, so they are accessible
     * only with their full namespaced names.
     *
     * ServiceManagerException is thrown if service is already registered.
     *
     * @param object $service
     * @throws ServiceManagerException
     */
    public function registerService($service, $name = null) {

        if (!is_object($service)) {
            throw new InvalidServiceException("Injected service is invalid.");
        }

        $reflection = new ReflectionClass($service);

        $namespaceName = '\\' . $reflection->getName();
        if (array_key_exists($namespaceName, $this->services)) {
            throw new ServiceAlreadyExistsException("Service $namespaceName already registered.");
        }
        $this->services[$namespaceName] = $service;

        $simpleName = $reflection->getShortName();
        if (!array_key_exists($simpleName, $this->services)) {
            $this->services[$simpleName] = $service;
        } else {
            // remove also already used simple name; this will force not found on request and use of full name
            unset($this->services[$simpleName]);
        }

    }

}