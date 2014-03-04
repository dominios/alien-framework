<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

class Option {

    const TYPE_SELECT = 0;
    const TYPE_RADIO = 1;

    private $type;
    private $name;
    private $value;
    private $text = '';
    private $selected = false;
    private $disabled = false;

    public function __construct($name, $type, $value) {
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
    }

    public function __toString() {
        $ret = '';
        switch ($this->type) {
            case Option::TYPE_SELECT:
                $ret .= '<option value="' . htmlspecialchars($this->getValue()) . '" ' . ($this->isSelected() ? 'selected' : '') . '>' . htmlspecialchars($this->getName()) . '</option>';
                break;
            case Option::TYPE_RADIO:
                $ret .= '<input type="radio" name="' . htmlspecialchars($this->getName()) . '" value="' . htmlspecialchars($this->getValue()) . '" ' . ($this->isSelected() ? 'checked' : '') . '>';
                break;
        }
        return $ret;
    }

    public function setDisabled($disabled) {
        $this->disabled = $disabled;
        return $this;
    }

    public function isDisabled() {
        return $this->disabled;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setSelected($selected) {
        $this->selected = (bool) $selected;
        return $this;
    }

    public function isSelected() {
        return (bool) $this->selected;
    }

    public function setText($text) {
        $this->text = $text;
        return $this;
    }

    public function getText() {
        return $this->text;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function getValue() {
        return $this->value;
    }


}