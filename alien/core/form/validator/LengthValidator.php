<?php

namespace Alien\Forms\Validator;

use Alien\Forms\Input;
use Alien\Forms\Validator;

class LengthValidator extends Validator {

    private $min;
    private $max;

    public function __construct($min, $max, $errorMessage = null) {
        $this->min = $min;
        $this->max = $max;
        $this->setErrorMessage($errorMessage);
    }

    public function validate(Input $input) {
        if (!is_null($this->min) && strlen($input->getValue()) < $this->min) {
            throw new ValidatorException("Entered value is shorter then minimum.");
        }
        if (!is_null($this->max) && strlen($input->getValue()) > $this->max) {
            throw new ValidatorException("Entered value is longer then maximum.");
        }
        return true;
    }

}