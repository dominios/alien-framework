<?php

namespace Alien\Annotaion;

abstract class Annotation {

    private $string;

    public function __construct($string) {
        $this->string = $string;
    }
}