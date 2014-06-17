<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

class Password extends Input {

    public function __construct($name, $defaultValue, $value = null, $size = null) {
        parent::__construct($name, 'text', $defaultValue, $value, $size);
        $this->type = 'password';
    }

    public function __toString() {
        $ret = '';
        $ret .= '<input type="password" name="' . $this->name . '" value="' . $this->value . '" ' . $this->commonRenderAttributes(true) . '>';
        return $ret;
    }
}