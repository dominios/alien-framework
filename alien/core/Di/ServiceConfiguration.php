<?php

namespace Alien\Di;

use Alien\Di\Exception\InvalidConfigurationException;
use Alien\Stdlib\Exception\NullException;
use InvalidArgumentException;

class ServiceConfiguration implements ServiceConfigurationInterface
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
     * Creates new service configuration.
     *
     * @param callable $factory function which creates service new instance.
     * @param string $name service identifier.
     * @param string[] $aliases [optional] array of service aliases.
     * @param bool $isShared [optional] if service is shared (default: true).
     * @param array $options [optional] any user defined options.
     */
    public function __construct(callable $factory, $name, array $aliases = [], $isShared = true, $options = [])
    {
        $this->factory = $factory;
        $this->name = $name;
        $this->aliases = $aliases;
        $this->isShared = $isShared;
        $this->options = $options;
    }

    /**
     * Builds service configuration from array.
     *
     * This method accepts multidimensional <i>associative array</i>. First level contains
     * only single element with <b>non numeric</b> string as it's key.
     *
     * Second level has only one required key: <b>factory</b> which returns function
     * (can use <code>\Alien\Di\ServiceLocatorInterface</code> as argument) which creates new instance of service.
     * Other fields are optional.
     *
     * Example of valid configuration with description:
     *
     * <pre>
     * [
     *  "MyService" => [                                                    // new service will have name 'MyService'
     *      "factory" => function (\Alien\ServiceLocatorInterface $sl) {    // factory function returns new instance
     *          return new MyService();                                     // can use ServiceLocator when needed
     *      },
     *      "aliases" => [                                                  // [optional] array of strings (alias names)
     *          "OtherServiceName", "CustomService"
     *      ],
     *      "shared" => false,                                            // [optional] if service is shared (default is true)
     *      "options" => [                                                  // [optional] any user defined values
     *          "foo" => "bar"
     *      ]
     *  ]
     * ];
     * </pre>
     *
     * @param array $configuration multidimensional array with correct structure.
     * @return ServiceConfigurationInterface
     */
    public static function createFromArray(array $configuration)
    {
        if (count($configuration) > 1) {
            throw new InvalidArgumentException("Service configuration array is invalid.");
        }

        $name = key($configuration);
        if (!is_string($name) || is_numeric($name)) {
            throw new InvalidConfigurationException(sprintf("Invalid service name given %s.", $name));
        }

        if (!array_key_exists('factory', $configuration[$name])) {
            throw new InvalidConfigurationException(sprintf("Missing factory function for service %s.", $name));
        }
        $factory = $configuration[$name]['factory'];

        $aliases = array_key_exists('aliases', $configuration[$name]) ? $configuration[$name]['aliases'] : [];
        $isShared = array_key_exists('shared', $configuration[$name]) ? $configuration[$name]['shared'] : true;
        $options = array_key_exists('options', $configuration[$name]) ? $configuration[$name]['options'] : [];

        return new ServiceConfiguration($factory, $name, $aliases, $isShared, $options);

    }

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
     * @param string $key option key to look for.
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