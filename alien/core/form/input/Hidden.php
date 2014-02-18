<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

class Hidden extends Input {

    public function __construct($name, $value = null) {
        parent::__construct($name, 'hidden', null, $value);
    }

    public function __toString() {
        $ret = '';
        $ret .= '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '">';
        return $ret;
    }

}