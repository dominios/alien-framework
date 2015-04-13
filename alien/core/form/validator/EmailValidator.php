<?php

namespace Alien\Forms\Validator;

use Alien\Forms\Validator;

/**
 * Class EmailValidator, validates if value is valid email address
 * @package Alien\Forms\Validator
 */
class EmailValidator extends RegexValidator {

    /**
     * Email address regex pattern
     */
    const PATTERN = "^[a-zA-Z][a-zA-Z0-9]*(\.[a-zA-Z0-9]+)*@[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)*\.[a-z]{2,3}$";

    const DEFAULT_ERROR_MESSAGE = 'Entered value is not valid email address.';

    /**
     * @param string|null $errorMessage
     */
    function __construct($errorMessage = null) {
        parent::__construct(EmailValidator::PATTERN, null, $errorMessage);
    }

}