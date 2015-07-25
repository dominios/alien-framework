<?php

namespace Alien\Di;

interface ServiceManagerInterface {

    public function registerService($service);

    public function getService($name);
}