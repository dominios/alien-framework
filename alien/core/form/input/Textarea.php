<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

class Textarea extends Input {

    public function __construct($name, $defaultValue, $value = null) {
        parent::__construct($name, 'textarea', $defaultValue, $value);
    }

    public function __toString() {
        $ret = '';
        $ret .= '<textarea name="' . $this->name . '" ' . $this->commonRenderAttributes(true) . '>';
        $ret .= $this->getValue();
        $ret .= '</textarea>';
        return $ret;
    }
}