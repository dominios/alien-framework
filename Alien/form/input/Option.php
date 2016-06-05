<?php

namespace Alien\Form\Input;

use Alien\Form\Input;

/**
 * Class Option, represents options for select and radio inputs
 * @package Alien\Forms\Input
 */
class Option {

    /**
     * Option for select input
     */
    const TYPE_SELECT = 0;

    /**
     *  Option for radio input
     */
    const TYPE_RADIO = 1;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $text = '';

    /**
     * @var bool
     */
    private $selected = false;

    /**
     * @var bool
     */
    private $disabled = false;

    /**
     * @param string $name
     * @param string $type
     * @param string $value
     */
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

    /**
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled($disabled) {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDisabled() {
        return $this->disabled;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param bool $selected
     * @return $this
     */
    public function setSelected($selected) {
        $this->selected = (bool) $selected;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSelected() {
        return (bool) $this->selected;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text) {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }


}