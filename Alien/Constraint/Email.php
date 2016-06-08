<?php

namespace Alien\Constraint;

/**
 * Validates string to correct email format.
 */
class Email extends Regex {

    /**
     * Email address regex pattern
     */
    const PATTERN = "^[a-zA-Z][a-zA-Z0-9]*(\.[a-zA-Z0-9]+)*@[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)*\.[a-z]{2,3}$";

    const ERROR_MESSAGE = 'Entered value is not valid email address.';

    public function __construct($modifiers)
    {
        parent::__construct(self::PATTERN, $modifiers);
    }

}