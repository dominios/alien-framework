<?php

namespace Alien;

use Alien\Exception\IOException;
use SplFileInfo;
use UnderflowException;
use UnexpectedValueException;

class Configuration implements ConfigurationInterface
{

    /**
     * @var array
     */
    private $config;

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
     * Merge given configurations into one
     *
     * Merges multiple configurations into single configuration.
     * New instance is returned when merged.
     *
     * <b>WARNING</b>: Order of arguments is important!
     *
     * @param Configuration $configuration,... configurations to merge
     * @return Configuration merged configuration
     */
    public function merge(Configuration $configuration)
    {
        if (!func_num_args()) {
            throw new UnderflowException("No arguments given");
        }

        $merged = [];
        foreach (func_get_args() as $configuration) {
            if ($configuration instanceof Configuration) {
                $merged = array_merge($merged, $configuration->config);
            } else {
                throw new UnexpectedValueException("Cannot merge other type then " . __CLASS__);
            }
        }

        $newConfig = new self;
        $newConfig->config = $merged;
        return $newConfig;

    }

}