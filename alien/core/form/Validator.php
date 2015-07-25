<?php

namespace Alien\Form;

use Alien\Form\Validator\CsrfValidator;
use Alien\Form\Validator\CustomValidator;
use Alien\Form\Validator\RegexValidator;
use Alien\Form\Validator\ValidatorException;

/**
 * Class Validator
 * @package Alien\Forms
 */
abstract class Validator {

    /**
     * @var string Message to print if validation fails
     */
    private $errorMessage;

    /**
     * @var bool if set to false, does not have influence on form validation status
     */
    private $chainBreaking = true;

    /**
     * Validates Input
     * @param Input $input
     * @return bool
     * @throws ValidatorException
     */
    public abstract function validate(Input $input);

    /**
     * Factory method for CSRF validator
     * @deprecated should use CsrfValidator directly
     * @return CsrfValidator
     */
    public static function csrf() {
        return new CsrfValidator();
    }

    /**
     * Factory method for Regex validator
     * @deprecated should use RegexValidator directly
     * @param string $pattern
     * @param null $errorMessage
     * @return RegexValidator
     */
    public static function regexp($pattern, $errorMessage = null) {
        return new RegexValidator($pattern, null, $errorMessage);
    }

    /**
     * Factory method for Custom validator
     * @deprecated should use CustomValidator directly
     * @param string $methodName
     * @param array $params
     * @param string|null $errorMessage
     * @return Validator
     */
    public static function custom($methodName, $params, $errorMessage = null) {
        return new CustomValidator($methodName, $params, $errorMessage);
    }

    /**
     * Sets message appended to Input if validation result eqauls false.
     * @param string $errorMessage
     * @return Validator
     */
    public function setErrorMessage($errorMessage) {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * Returns error message
     * @return string
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * Sets if should continue validating in case of validation result equals to false
     * @param bool $breakChain
     * @return $this
     */
    public function setIsChainBreaking($breakChain) {
        $this->chainBreaking = $breakChain;
        return $this;
    }

    /**
     * Returns if should stop validating in first false result
     * @return boolean
     */
    public function isChainBreaking() {
        return $this->chainBreaking;
    }


}

