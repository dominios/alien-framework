<?php

namespace Alien\Constraint;

use Alien\Constraint\Exception\ValidationException;

interface ConstraintInterface
{
    /**
     * Perform validation upon given value.
     * @param mixed $value tested value.
     * @return bool <code>true</code> on success.
     * @throws ValidationException on validation failure.
     */
    public function validate($value);

}