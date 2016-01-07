<?php

namespace Alien\Di;

use Alien\Stdlib\Exception\NullException;

class Service implements ServiceConfigurationInterface
{

    /**
     * Name of the service.
     * @var string
     */
    protected $name;

    /**
     * Array of available aliases for service.
     * @var string[]
     */
    protected $aliases;

    /**
     * Factory method for service.
     * @var callable
     */
    protected $factory;

    /**
     * If service should be shared.
     * @var bool
     */
    protected $isShared = true;

    /**
     * Array of user defined options.
     * @var
     */
    protected $options;

    /**
     * Returns identifier of service.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns alias name of service.
     *
     * If service has no alias, <code>null</code> is returned.
     *
     * @return null|string
     */
    public function getAliases()
    {
        return array_values($this->aliases);
    }

    /**
     * Checks if service has given alias name.
     *
     * @param string $alias needle
     * @return bool
     */
    public function hasAlias($alias)
    {
        return in_array($alias, $this->aliases);
    }

    /**
     * Returns function with creates new service instance.
     *
     * Execution of returned function creates new instance of service.
     *
     * @return callable
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Specify if service should have only one instance, or create new instance on each call.
     *
     * @return bool
     */
    public function isShared()
    {
        return $this->isShared;
    }

    /**
     * Returns any user defined option by key.
     *
     * @param string $key option key to look for
     * @return mixed
     * @throws NullException when option is not defined.
     */
    public function getOption($key)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new NullException(sprintf("Option %s is not defined for service.", $key));
        }
        return $this->options[$key];
    }

}