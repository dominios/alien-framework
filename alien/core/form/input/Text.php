<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

class Text extends Input {

    public function __construct($name, $defaultValue, $value = null, $size = null) {
        parent::__construct($name, 'text', $defaultValue, $value, $size);
    }

    public function __toString() {
        $attr = $this->commonRenderAttributes(true);
        $ret = '';
        $ret .= '<input type="text" name="' . $this->name . '" value="' . $this->value . '" ' . implode(' ', $attr) . '>';
        return $ret;
    }
}