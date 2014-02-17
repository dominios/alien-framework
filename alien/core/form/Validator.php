<?php

namespace Alien\Forms;

use Alien\Models\Content\Template;
use PDO;
use Alien\Alien;
use Alien\DBConfig;

class Validator {

    const PATTERN_EMAIL = "/^[a-zA-Z][a-zA-Z0-9]*(\.[a-zA-Z0-9]+)*@[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)*\.[a-z]{2,3}$/";

    private $errorMessage;
    private $pattern;
    private $type;
    private $methodName;
    private $params;

    private function __construct() {
        ;
    }

    public function validate(Input $input) {
        $ret = false;
        if ($this->pattern != '') {
            $ret = preg_match($this->pattern, $input->getValue()) ? true : false;
        }
        if ($this->type != '') {
            $fn = 'is_' . $this->type;
            $ret = $fn($input->getValue()) ? true : false;
        }
        if ($this->methodName != '' && method_exists($this, $this->methodName)) {
            $fn = $this->methodName;
            $ret = $this->{$fn}($input);
        }
        if ($ret == false) {
            $this->printErrorMessage($input);
        }
        return $ret;
    }

    public static function regexp($pattern, $errorMessage = null) {
        $validator = new self;
        $validator->pattern = $pattern;
        $validator->errorMessage = $errorMessage;
        return $validator;
    }

    public static function type($type, $errorMessage = null) {
        $validator = new self;
        $validator->type = $type;
        $validator->errorMessage = $errorMessage;
        return $validator;
    }

    public static function custom($methodName, $params, $errorMessage = null) {
        $validator = new self;
        $validator->methodName = $methodName;
        $validator->params = $params;
        $validator->errorMessage = $errorMessage;
        return $validator;
    }

    private function printErrorMessage(Input $input) {
        $input->setErrorMessage($this->errorMessage);
    }

    protected function userUniqueEmail(Input $input) {
        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::USERS) . ' WHERE email=:e && id_u!=:u LIMIT 1');
        $Q->bindValue(':e', $input->getValue(), PDO::PARAM_STR);
        $Q->bindValue(':u', (int) $this->params['ignoredUserId'], PDO::PARAM_INT);
        $Q->execute();
        $ret = $Q->rowCount();
        if (!$ret) {
            $this->printErrorMessage($input);
        }
        return $ret ? false : true;
    }

    protected function templateUniqueName(Input $input) {
        $result = Template::isTemplateNameInUse($input->getValue(), $this->params['ignore']);
        if ($result) {
            $this->printErrorMessage($input);
            return false;
        } else {
            return true;
        }
    }

    protected function between(Input $input) {
        $result = true;
        $min = (int) $this->params['min'];
        $max = (int) $this->params['max'];
        $value = (int) $input->getValue();
        if ($max <= $min) {
//            throw new \Exception('Maximum number cannot by lower then minimum.');
        }
        if ($value < $min) {
            $result &= false;
        }
        if ($value > $max && $max > 0) {
            $result &= false;
        }
        if (!$result) {
            $this->printErrorMessage($input);
            return false;
        }
        return true;
    }

    protected function stringLength(Input $input) {
        $result = true;
        $min = (int) $this->params['min'];
        $max = (int) $this->params['max'];
        $length = strlen($input->getValue());
        if ($max < $min && $max > 0) {
            throw new \Exception('Maximum number cannot by lower then minimum.');
        }
        if ($length < $min) {
            $result &= false;
        }
        if ($length > $max && $max > 0) {
            $result &= false;
        }
        if (!$result) {
            $this->printErrorMessage($input);
            return false;
        }
        return true;
    }

    protected function notEmpty(Input $input){
        $value = $input->getValue();
        if(mb_strlen($value)){
            return true;
        } else {
            $this->printErrorMessage($input);
            return false;
        }
    }
}

