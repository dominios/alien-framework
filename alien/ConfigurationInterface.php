<?php

namespace Alien;

interface ConfigurationInterface
{

    /**
     * Returns value of single parameter identified by it's key
     * @param $key string
     * @return mixed
     */
    public function get($key);

}