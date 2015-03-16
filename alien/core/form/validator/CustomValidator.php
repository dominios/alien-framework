<?php

namespace Alien\Forms\Validator;

use Alien\Application;
use Alien\DBConfig;
use Alien\Forms\Input;
use Alien\Forms\Validator;
use Alien\Models\Content\Page;
use Alien\Models\Content\Template;
use PDO;

class CustomValidator extends Validator {

    private $methodName;
    private $params;

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

    protected function userUniqueEmail(Input $input) {
        $app = Application::getInstance();
        $DBH = $app->getServiceManager()->getService('PDO');
        $Q = $DBH->prepare('SELECT * FROM test_users WHERE email=:e && id_u<>:u LIMIT 1');
        $Q->bindValue(':e', $input->getValue(), PDO::PARAM_STR);
        $Q->bindValue(':u', (int) $this->params['ignoredUserId'], PDO::PARAM_INT);
        $Q->execute();
        return !($Q->rowCount());
    }

    protected function templateUniqueName(Input $input) {
        return (bool) Template::isTemplateNameInUse($input->getValue(), $this->params['ignore']);
    }

    protected function pageSeolink(Input $input) {
        return (bool) Page::isSeolinkInUse($input->getValue(), $this->params['ignore']);
    }
}