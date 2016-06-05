<?php

namespace Alien\Form\Input;

use Alien\Form\Input;

/**
 * Class Textarea, represents HTML textarea
 * @package Alien\Forms\Input
 */
class Textarea extends Input {

    /**
     * @param string $name
     * @param string $defaultValue
     * @param string|null $value
     */
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