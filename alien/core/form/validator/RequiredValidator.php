<?php

namespace Alien\Form\Validator;

use Alien\Form\Input;
use Alien\Form\Validator;

/**
 * Class RequiredValidator, tests if is set value of given input and not equals false
 * @package Alien\Forms\Validator
 */
class RequiredValidator extends Validator {

    const DEFAULT_ERROR_MESSAGE = "Column %name% is required.";

    /**
     * @param string|null $errorMessage
     */
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