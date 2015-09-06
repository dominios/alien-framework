<?php

namespace Alien\Di;

/**
 * ServiceLocatorInterface
 * @package Alien\Di
 */
interface ServiceLocatorInterface
{

    /**
     * Register instance provided by argument as service
     * @param $service object
     * @return void
     */
    public function registerService($service);

    /**
     * Returns registered service by name
     * @param $name string service identifier
     * @return object
     */
    public function getService($name);
}