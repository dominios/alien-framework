<?php

namespace Alien\Annotaion;

class Auth extends Annotation {

    public function __construct() {

    }

    public function __invoke() {
        $args = func_get_args();
        var_dump($args);
    }
}