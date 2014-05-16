<?php

namespace Alien\Forms;

use Alien\Forms\Validator\CsrfValidator;
use Alien\Forms\Validator\CustomValidator;
use Alien\Forms\Validator\RegexValidator;
use Alien\Forms\Validator\ValidatorException;
use Alien\Models\Content\Page;
use Alien\Models\Content\Template;
use Alien\Notification;
use PDO;
use Alien\Application;
use Alien\DBConfig;

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
     * @param Input $input
     * @return bool
     * @throws ValidatorException
     */
    public abstract function validate(Input $input);

    /**
     * @deprecated
     * @return CsrfValidator
     */
    public static function csrf() {
        return new CsrfValidator();
    }

    /**
     * @deprecated
     * @param $pattern
     * @param null $errorMessage
     * @return RegexValidator
     */
    public static function regexp($pattern, $errorMessage = null) {
        return new RegexValidator($pattern, null, $errorMessage);
    }

    /**
     * @deprecated
     * @param $methodName
     * @param $params
     * @param null $errorMessage
     * @return Validator
     */
    public static function custom($methodName, $params, $errorMessage = null) {
        return new CustomValidator($methodName, $params, $errorMessage);
    }

    /**
     * @param string $errorMessage
     * @return Validator
     */
    public function setErrorMessage($errorMessage) {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }


}

