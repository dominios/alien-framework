<?php

namespace Alien;

use Alien\Exception\IOException;
use InvalidArgumentException;
use SplFileInfo;
use UnexpectedValueException;

class Configuration implements ConfigurationInterface
{

    /**
     * @var array
     */
    private $config;

    /**
     * Crates new instance of Configuration
     *
     * Multiple arguments can be passed to constructor. Constructor accepts variable
     * count of arguments (also none). You can pass arrays or other instances of
     * <code>Configuration</code>. Result will be merge of all given arguments.
     *
     * @param array|Configuration|SplFileFinfo $configurations,... configrutions to merge
     * @throws InvalidArgumentException when any of arguments is of unsupported type
     */
    public function __construct()
    {
        $merged = [];
        if (func_num_args()) {
            $args = func_get_args();
            foreach ($args as $arg) {
                if (is_array($arg)) {
                    $merged = array_merge($merged, $arg);
                } else if ($arg instanceof Configuration) {
                    $toMerge = $arg->config;
                    $merged = array_merge($merged, $toMerge);
                } else if ($arg instanceof SplFileInfo) {
                    // beware: this configuration is temporarily overriden when calling this method!
                    $this->loadConfigurationFromFile($arg);
                    $toMerge = $this->config;
                    $merged = array_merge($merged, $toMerge);
                } else {
                    throw new InvalidArgumentException("Invalid type " . gettype($arg) . " given to make merged configuration");
                }
            }
        }
        $this->config = $merged;
    }

    /**
     * Loads configuration from given file
     *
     * Targeted file should return php array.
     *
     * @param SplFileInfo $finfo
     * @throws IOException when file is not readable
     * @throws IOException when argument is not file
     * @throws UnexpectedValueException when file does not contain valid array
     */
    public function loadConfigurationFromFile(SplFileInfo $finfo)
    {
        if (!$finfo->isReadable()) {
            throw new IOException("File is not readable");
        }
        if (!$finfo->isFile()) {
            throw new IOException("Argument is not valid file");
        }
        $conf = include $finfo->getPath() . '/' . $finfo->getBasename();
        if (!is_array($conf)) {
            throw new UnexpectedValueException("Configuration is not an array");
        }
        $this->config = $conf;
    }

    /**
     * Returns value of single parameter identified by it's key
     * @param $key string
     * @return mixed
     */
    public function get($key)
    {
        return $this->config[$key];
    }

    /**
     * Merge current configuration with given configurations
     *
     * When calling this method, current settings are merged directly with
     * other configurations.
     *
     * <b>WARNING</b>: Order of arguments is important!
     *
     * @param Configuration $configuration,... configurations to merge with
     * * @throws InvalidArgumentException when no configurations given
     */
    public function mergeWith(Configuration $configuration)
    {
        if (!func_num_args()) {
            throw new InvalidArgumentException("No arguments given");
        }

        $merged = $this;
        $arguments = func_get_args();
        foreach ($arguments as $arg) {
            $merged = $this->merge($merged, $arg);
        }
        $this->config = $merged->config;
    }

    /**
     * Merge given configurations into one
     *
     * Merges multiple configurations into single configuration.
     * New instance is returned when merged.
     *
     * <b>WARNING</b>: Order of arguments is important!
     *
     * @param Configuration $configuration,... configurations to merge
     * @return Configuration merged configuration
     * @throws InvalidArgumentException when no configurations given
     */
    public function merge(Configuration $configuration)
    {
        if (!func_num_args()) {
            throw new InvalidArgumentException("No arguments given");
        }

        $merged = [];
        $arguments = func_get_args();
        foreach ($arguments as $configuration) {
            if ($configuration instanceof Configuration) {
                if (is_array($configuration->config)) {
                    $merged = array_merge($merged, $configuration->config);
                }
            } else {
                throw new UnexpectedValueException("Cannot merge other type then " . __CLASS__ . ", " . get_class($configuration) . ' given');
            }
        }

        $newConfig = new self;
        $newConfig->config = $merged;
        return $newConfig;

    }

}