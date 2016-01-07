<?php

namespace Alien\Di;

/**
 * Interface ServiceLocatorInterface.
 */
interface ServiceLocatorInterface
{

    /**
     * Register object provided by argument as service.
     *
     * @param object $service instance of any object.
     * @param string $name identifier of service.
     * @return
     */
    public function register($service, $name);

    /**
     * Returns registered service by name.
     *
     * @param string $name service identifier.
     * @return mixed service instance.
     */
    public function get($name);

}