<?php

namespace Alien\Forms\Validator;

use Alien\Forms\Validator;

class EmailValidator extends RegexValidator {

    const PATTERN = "^[a-zA-Z][a-zA-Z0-9]*(\.[a-zA-Z0-9]+)*@[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)*\.[a-z]{2,3}$";
    const DEFAULT_ERROR_MESSAGE = 'Entered value is not valid email address.';

    function __construct($errorMessage = null) {
        parent::__construct(EmailValidator::PATTERN, null, $errorMessage);
    }

}