<?php

namespace Alien\Forms;

use PDO;
use Alien\Alien;
use Alien\DBConfig;

class Validator {

    const PATTERN_EMAIL = "/^[a-zA-Z][a-zA-Z0-9]*(\.[a-zA-Z0-9]+)*@[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)*\.[a-z]{2,3}$/";

    private $pattern;
    private $type;
    private $methodName;
    private $params;

    private function __construct() {
        ;
    }

    public function validate(Input $input) {
        if ($this->pattern != '') {
            return preg_match($this->pattern, $input->getValue()) ? true : false;
        }
        if ($this->type != '') {
            $fn = 'is_' . $this->type;
            return $fn($input->getValue()) ? true : false;
        }
        if ($this->methodName != '' && method_exists($this, $this->methodName)) {
            $fn = $this->methodName;
            return $this->{$fn}($input);
        }
        return false; // nemalo by nikdy nastat, ale istota
    }

    public static function regexp($pattern) {
        $validator = new self;
        $validator->pattern = $pattern;
        return $validator;
    }

    public static function type($type) {
        $validator = new self;
        $validator->type = $type;
        return $validator;
    }

    public static function custom($methodName, $params) {
        $validator = new self;
        $validator->methodName = $methodName;
        $validator->params = $params;
        return $validator;
    }

    protected function userUniqueEmail(Input $input) {
        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::USERS) . ' WHERE email=:e && id_u!=:u LIMIT 1');
        $Q->bindValue(':e', $input->getValue(), PDO::PARAM_STR);
        $Q->bindValue(':u', (int) $this->params['ignoredUserId'], PDO::PARAM_INT);
        $Q->execute();
        return $Q->rowCount() ? false : true;
    }

}

