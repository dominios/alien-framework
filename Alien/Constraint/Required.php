<?php

namespace Alien\Constraint;

use Alien\Constraint\Exception\ValidationException;

class Required
{

    /**
     * Creates new instance of required constraint.
     */
    public function __construct()
    {
    }

    public function validate($value)
    {
        if (is_null($value)) {
            throw new ValidationException('Value cannot be null.');
        }
        if (is_string($value) && trim($value) === "") {
            throw new ValidationException('Given string must not be empty.');
        }
        if (is_array($value) && !count($value)) {
            throw new ValidationException('Array must not be empty.');
        }
        return true;
    }
}