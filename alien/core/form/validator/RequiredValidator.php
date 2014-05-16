<?php

namespace Alien\Forms\Validator;

use Alien\Forms\Input;
use Alien\Forms\Validator;

class RequiredValidator extends Validator {

    const DEFAULT_ERROR_MESSAGE = "Column %name% is required.";

    public function __construct($errorMessage = null) {
        if ($errorMessage !== null) {
            $this->setErrorMessage($errorMessage);
        }
    }

    public function validate(Input $input) {
        $value = $input->getValue();
        if (is_null($value) || (is_string($value) && !mb_strlen($value))) {
            throw new ValidatorException(strlen($this->getErrorMessage()) ? $this->getErrorMessage() : str_replace('%name%', $input->getName(), RequiredValidator::DEFAULT_ERROR_MESSAGE));
        }
        return true;
    }

}