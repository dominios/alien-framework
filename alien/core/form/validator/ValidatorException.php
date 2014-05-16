<?php

namespace Alien\Forms\Validator;

use Exception;

class ValidatorException extends \Exception {
    
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}