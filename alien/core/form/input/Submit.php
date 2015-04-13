<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

/**
 * Class Submit, represents HTML input type submit
 * @package Alien\Forms\Input
 */
class Submit extends Input {

    /**
     * @param string $name
     * @param string|null $value
     */
    public function __construct($name = '', $value = null) {
        parent::__construct($name, 'submit', $value, null, null);
    }

    public function __toString() {
        $ret = '';
        $ret .= '<input type="submit" name="' . $this->getName() . '" value="' . $this->getValue() . '">';
        return $ret;
    }
}