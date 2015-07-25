<?php

namespace Alien\Form\Validator;

use Alien\Form\Input;
use Alien\Form\Validator;

/**
 * Class RangeValidator, valdidates given number against minimum and maximum value
 * @package Alien\Forms\Validator
 */
class RangeValidator extends Validator {

    /**
     * @var float
     */
    private $min;

    /**
     * @var float
     */
    private $max;

    /**
     * @param float $min
     * @param float $max
     * @param string|null $errorMessage
     */
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