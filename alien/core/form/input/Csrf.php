<?php

namespace Alien\Form\Input;

use Alien\Form\Validator;

/**
 * Class Csrf, use for preventing agains Cross-Site Request Forgery attacks
 * @package Alien\Forms\Input
 */
class Csrf extends Hidden {

    /**
     * Timetout time for one token in seconds
     */
    const DEFAULT_TOKEN_TIMEOUT = 3600;

    /**
     * @param int|null $tokenTimeout
     */
    public function __construct($tokenTimeout = null) {
        $token = $this->generateToken($tokenTimeout);
        parent::__construct('csrfToken', $token);
        $this->addValidator(Validator::csrf());
    }

    /**
     * Generate new token
     * @param int $tokenTimeout
     * @return mixed
     */
    private function generateToken($tokenTimeout = null) {
        $token = array(
            'token' => $this->rand_chars(),
            'timeout' => time() + ($tokenTimeout === null ? Csrf::DEFAULT_TOKEN_TIMEOUT : $tokenTimeout)
        );
        $_SESSION['tokens'][] = $token;
        return $token['token'];
    }

    /** Vygenerování náhodného řetězce
     * @param int [$count] délka vráceného řetězce
     * @param int [$chars] použité znaky: <=10 číslice, <=36 +malá písmena, <=62 +velká písmena
     * @return string náhodný řetězec
     * @copyright Jakub Vrána, http://php.vrana.cz
     */
    private function rand_chars($count = 16, $chars = 36) {
        $return = "";
        for ($i = 0; $i < $count; $i++) {
            $rand = rand(0, $chars - 1);
            $return .= chr($rand + ($rand < 10 ? ord('0') : ($rand < 36 ? ord('a') - 10 : ord('A') - 36)));
        }
        return $return;
    }
}