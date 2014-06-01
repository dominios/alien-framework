<?php

namespace Alien;

use Alien\ServiceException;
use Alien\Db\CRUDDao;
use DomainException;
use InvalidArgumentException;

final class ServiceManager {

    private static $instance = null;
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
     *
     * @param string $name service name
     * @throws ServiceException
     * @return object
     */
    public function getService($name) {
        if (!array_key_exists($name, $this->services)) {
            throw new ServiceException("Requested service $name is not available.");
        }
        return $this->services[$name];
    }

    /**
     * Gets DAO service by name. Due to added type-check, it's more IDE-friendly then simple getServce()
     *
     * @param string $name service name
     * @throws ServiceException
     * @return \Alien\Db\CRUDDaoImpl
     */
    public function getDao($name) {
        $dao = $this->getService($name);
        if ($dao instanceof CRUDDao) {
            return $dao;
        } else {
            throw new ServiceException("No DAO service registered with name '$name'.");
        }
    }

    /**
     * Register any object as service. Every object can be registered only once.
     *
     * @param object $service
     * @throws InvalidArgumentException
     */
    public function registerService($service) {
        if (!is_object($service)) {
            throw new InvalidArgumentException("Injected service is invalid.");
        }
        $className = get_class($service);
        if (array_key_exists($className, $this->services)) {
            throw new InvalidArgumentException("Service already registered.");
        }
        $this->services[$className] = $service;
    }

}