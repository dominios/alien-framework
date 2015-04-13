<?php

namespace Alien\Forms\Validator;

use Alien\Forms\Input;
use Alien\Forms\Validator;

/**
 * Class CsrfValidator, protects against Cross-Site Request Forgery attacks
 * @package Alien\Forms\Validator
 */
class CsrfValidator extends Validator {

    public function validate(Input $input) {
        $valid = false;
        foreach ($_SESSION['tokens'] as $key => $token) {
            if ($token['timeout'] < time()) {
                if ($token['token'] == $input->getValue()) {
                    throw new ValidatorException("CSRF token timeout.");
                }
                unset($_SESSION['tokens'][$key]);
                continue;
            }
            if ($token['token'] == $input->getValue()) {
                $valid = true;
                unset($_SESSION['tokens'][$key]);
            }
        }
        if (!$valid) {
            throw new ValidatorException('Invalid CSRF token!');
        }
        return true;
    }

}