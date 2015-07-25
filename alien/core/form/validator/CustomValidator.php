<?php

namespace Alien\Form\Validator;

use Alien\Application;
use Alien\Form\Input;
use Alien\Form\Validator;
use PDO;

/**
 * Class CustomValidator, wraps custom validation methods in single class.
 * @package Alien\Forms\Validator
 */
class CustomValidator extends Validator {

    /**
     * @var string name of this class method to call
     */
    private $methodName;

    /**
     * @var array custom params
     */
    private $params;

    /**
     * @param string $methodName
     * @param array $params
     * @param string|null $errorMessage
     */
    public function  __construct($methodName, $params, $errorMessage = null) {
        $this->methodName = $methodName;
        $this->params = $params;
        $this->setErrorMessage($errorMessage);
    }

    public function validate(Input $input) {
        if (!method_exists($this, $this->methodName)) {
            throw new \BadMethodCallException("Validator $this->methodName does not exists!");
        }
        $fn = $this->methodName;
        if (!$this->{$fn}($input)) {
            throw new ValidatorException($this->getErrorMessage());
        }
        return true;
    }

    /**
     * Checks if given input's value is unique email in database.
     * @param Input $input
     * @return bool
     */
    protected function userUniqueEmail(Input $input) {
        $app = Application::getInstance();
        $DBH = $app->getServiceManager()->getService('PDO');
        $Q = $DBH->prepare('SELECT * FROM test_users WHERE email=:e && id_u<>:u LIMIT 1');
        $Q->bindValue(':e', $input->getValue(), PDO::PARAM_STR);
        $Q->bindValue(':u', (int) $this->params['ignoredUserId'], PDO::PARAM_INT);
        $Q->execute();
        return !($Q->rowCount());
    }

}