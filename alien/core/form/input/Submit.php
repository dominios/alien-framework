<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

class Submit extends Input {

    public function __construct($name = '', $value = null) {
        parent::__construct($name, 'submit', $value, null, null);
    }

    public function __toString() {
        $ret = '';
        $ret .= '<input type="submit" name="' . $this->getName() . '" value="' . $this->getValue() . '">';
        return $ret;
    }
}