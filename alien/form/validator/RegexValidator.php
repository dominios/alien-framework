<?php

namespace Alien\Form\Validator;

use Alien\Form\Input;
use Alien\Form\Validator\Exception\ValidatorException;

/**
 * Class RegexValidator, validates given string input against regular expression
 * @package Alien\Forms\Validator
 */
class RegexValidator extends Validator {

    const DEFAULT_ERROR_MESSAGE = 'Entered value does not match regular expression.';

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var string
     */
    private $modifiers;

    /**
     * @param string $pattern
     * @param string|null $modifiers
     * @param string|null $errorMessage
     */
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

    /**
     * @param string $modifiers
     * @return $this
     */
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