<?php

namespace Alien;

use InvalidArgumentException;
use SplFileInfo;
use UnexpectedValueException;

class Configuration {

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
     * @throws InvalidArgumentException when file is not readable
     * @throws InvalidArgumentException when argument is not file
     * @throws UnexpectedValueException when file does not contain valid array
     */
    public function loadConfigurationFromFile(SplFileInfo $finfo) {
        if(!$finfo->isReadable()) {
            throw new InvalidArgumentException("File is not readable.");
        }
        if(!$finfo->isFile()) {
            throw new InvalidArgumentException("Argument is not valid file.");
        }
        $conf = include $finfo->getBasename();
        if(!is_array($conf)) {
            throw new UnexpectedValueException("Configuration is not an array.");
        }
        $this->config = $conf;
    }

    /**
     * Returns value of single parameter identified by it's key
     * @param $key string
     * @return mixed
     */
    public function get($key) {
        return $this->config[$key];
    }

}