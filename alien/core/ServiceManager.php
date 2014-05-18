<?php

namespace Alien;

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
     * @param string $name needle
     * @return object
     * @throws DomainException
     */
    public function getService($name) {
        if (!array_key_exists($name, $this->services)) {
            throw new DomainException("Requested service is not available.");
        }
        return $this->services[$name];
    }

    /**
     * Alias of getService(). Changed only PHPDoc return type, so it is more useful for IDE.
     *
     * @param $name
     * @return \Alien\Db\CRUDDaoImpl
     */
    public function getDao($name) {
        return $this->getService($name);
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