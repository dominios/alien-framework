<?php

namespace Alien\Forms\Validator;

use Alien\Forms\Input;
use Alien\Forms\Validator;

class RegexValidator extends Validator {

    const DEFAULT_ERROR_MESSAGE = 'Entered value does not match regular expression.';

    private $pattern;
    private $modifiers;

    public function __construct($pattern, $modifiers = null, $errorMessage = null) {
        $this->pattern = $pattern;
        $this->setErrorMessage(is_null($errorMessage) ? self::DEFAULT_ERROR_MESSAGE : $errorMessage);
    }

    public function validate(Input $input) {
        if (!preg_match("/" . $this->pattern . "/" . (string) $this->modifiers, $input->getValue())) {
            throw new ValidatorException($this->getErrorMessage());
        }
        return true;
    }

    public function setModifiers($modifiers) {
        $this->modifiers = $modifiers;
        return $this;
    }

    /**
     * @return string
     */
    public function getModifiers() {
        return $this->modifiers;
    }


}