<?php

namespace Alien\Form\Input;

use Alien\Form\Input;

/**
 * Class Password, represents HTML input type password
 * @package Alien\Forms\Input
 */
class Password extends Input {

    /**
     * @param string $name
     * @param string $defaultValue
     * @param string|null $value
     * @param int|null $size
     */
    public function __construct($name, $defaultValue, $value = null, $size = null) {
        parent::__construct($name, 'text', $defaultValue, $value, $size);
        $this->type = 'password';
    }

    public function __toString() {
        $this->addCssClass('form-control');
        $ret = '';
        $ret .= '<input type="password" name="' . $this->name . '" value="' . $this->value . '" ' . $this->commonRenderAttributes(true) . '>';
        return $ret;
    }
}