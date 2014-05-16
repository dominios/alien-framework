<?php

namespace Alien\Forms\Validator;

use Alien\Forms\Input;
use Alien\Forms\Validator;

class RangeValidator extends Validator {

    private $min;
    private $max;

    public function __construct($min, $max, $errorMessage = null) {
        $this->min = $min;
        $this->max = $max;
        $this->setErrorMessage($errorMessage);
    }

    public function validate(Input $input) {
        if (!is_null($this->min) && $input->getValue() < $this->min) {
            throw new ValidatorException("Entered value is lower then minimum.");
        }
        if (!is_null($this->max) && $input->getValue() > $this->max) {
            throw new ValidatorException("Entered value is greater then maximum.");
        }
        return true;
    }

}