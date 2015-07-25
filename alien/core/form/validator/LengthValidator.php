<?php

namespace Alien\Form\Validator;

use Alien\Form\Input;
use Alien\Form\Validator;

/**
 * Class LengthValidator, validates string input against given minimum and maximum length
 * @package Alien\Forms\Validator
 */
class LengthValidator extends Validator {

    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $max;

    /**
     * @param int $min
     * @param int $max
     * @param string|null $errorMessage
     */
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