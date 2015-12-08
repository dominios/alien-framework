<?php

namespace Alien\Di;

/**
 * Provides access to Service Locator in objects.
 *
 * <b>WARNING:</b> Currently, there is no auto mechanism to inject service locator into any object but might be added in future.
 * Setting of ServiceLocator must be done implicitly.
 */
interface ServiceLocatorAwareInterface
{

    /**
     * Injects Service Locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator);

    /**
     * Retrieve Service Locator instance.
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator();
}