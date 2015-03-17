<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

class Color extends Input {

    public function __construct($name, $defaultValue, $value = null, $size = null) {
        parent::__construct($name, 'text', $defaultValue, $value, $size);
        $this->type = 'color';
        $this->setCssStyle('width: 75px;');
    }

    public function __toString() {
        $this->addCssClass('form-control');
        $ret = '';
        $ret .= '<input type="color" name="' . $this->name . '" value="' . $this->value . '" ' . $this->commonRenderAttributes(true) . '>';
        return $ret;
    }

    public function getValue() {
        return str_replace('#', '', parent::getValue());
    }

}