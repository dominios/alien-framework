<?php

namespace Alien\Di;

/**
 * Interface defining each service.
 */
interface ServiceConfigurationInterface
{

    /**
     * Returns identifier of service.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns alias name of service.
     *
     * If service has no alias, <code>null</code> is returned.
     *
     * @return null|string
     */
    public function getAliases();

    /**
     * Returns function with creates new service instance.
     *
     * Execution of returned function creates new instance of service.
     *
     * @return callable
     */
    public function getFactory();

    /**
     * Specify if service should have only one instance, or create new instance on each call.
     *
     * @return bool
     */
    public function isShared();

    /**
     * Returns any user defined option by key.
     *
     * @param string $key option key to look for.
     * @return mixed
     */
    public function getOption($key); // get any custom option defined

}