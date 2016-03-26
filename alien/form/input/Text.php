<?php

namespace Alien\Form\Input;

use Alien\Form\Input;

/**
 * Class Text, represents default HTML input type text
 * @package Alien\Forms\Input
 */
class Text extends Input {

    /**
     * @param string $name
     * @param string $defaultValue
     * @param string $value
     * @param int|null $size
     */
    public function __construct($name, $defaultValue, $value = null, $size = null) {
        parent::__construct($name, 'text', $defaultValue, $value, $size);
    }

    public function __toString() {
        $this->addCssClass('form-control');
        return '<input type="text" name="' . $this->name . '" value="' . $this->value . '" ' . $this->commonRenderAttributes(true) . '>';
    }

}