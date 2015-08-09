<?php

namespace Alien\Di;

interface ServiceLocatorInterface {

    public function registerService($service);

    public function getService($name);
}