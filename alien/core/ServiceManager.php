<?php

namespace Alien;

use Alien\Db\CRUDDao;
use ReflectionClass;

interface IServiceManager {
    public function registerService($service);

    public function getService($name);
}

final class ServiceManager implements IServiceManager {

    /**
     * @var ServiceManager
     */
    private static $instance = null;

    /**
     * @var array
     */
    private $services = array();

    private function __construct() {
    }

    private function __clone() {
    }

    /**
     * Initialize the ServiceManager
     *
     * @param array $config currently not used, but can change in future
     * @return ServiceManager
     */
    public static function initialize(array $config) {
        if (self::$instance === null) {
            self::$instance = new ServiceManager;
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
        $test = strpos($name, '\\');
        if (!$test) {
            if (!array_key_exists($name, $this->services)) {
                throw new ServiceManagerException("Multiple services with name $name registered, namespace required.");
            }
        } elseif ($test != 0) {
            $name = '\\' . $name;
        }
        if (!array_key_exists($name, $this->services)) {
            throw new ServiceManagerException("Requested service $name is not available.");
        }
        return $this->services[$name];
    }

    /**
     * Gets DAO service by name. Due to added type-check, it's more IDE-friendly then simple ServiceManager::getServce().
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
            throw new ServiceManagerException("No DAO service registered with name '$name'.");
        }
    }

    /**
     * Register any object as service. Any object can be registered only once.
     * Registered services may be called by ServiceManager::getService() method using their full name (with namespace) or with simple name,
     * however registering two different services with same simple name leads to disable this feature for such classes, so they are accessible
     * only with their full namespaced names.
     *
     * ServiceManagerException is thrown if service is already registered.
     *
     * @param object $service
     * @throws ServiceManagerException
     */
    public function registerService($service) {
        if (!is_object($service)) {
            throw new ServiceManagerException("Injected service is invalid.");
        }

        $reflection = new ReflectionClass($service);

        $namespaceName = '\\' . $reflection->getName();
        if (array_key_exists($namespaceName, $this->services)) {
            throw new ServiceManagerException("Service $namespaceName already registered.");
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