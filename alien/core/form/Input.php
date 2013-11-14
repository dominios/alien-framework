<?php

namespace Alien\Forms;

class Input {

    private $name;
    private $type;
    private $defaultValue;
    private $value;
    private $size;
    private $disabled = false;
    private $placeholder;
    private $autocomplete = true;
    private $cssClass = array();
    private $cssStyle;
    private $validators = array();

    private function __construct($name, $type, $defaultValue, $value = null, $size = null) {
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
        $this->size = $size;
        $this->defaultValue = $defaultValue;
    }

    public static function hidden($name, $defaultValue, $value = null) {
        $input = new self($name, 'hidden', $defaultValue, $value);
        return $input;
    }

    public static function password($name, $defaultValue, $value = null, $size = null) {
        $input = self::text($name, $defaultValue, $value, $size);
        $input->type = 'password';
        return $input;
    }

    public static function text($name, $defaultValue, $value = null, $size = null) {
        $input = new self($name, 'text', $defaultValue, $value, $size);
        return $input;
    }

    public static function select() {

    }

    public static function textarea() {

    }

    public static function checkbox() {

    }

    public static function radio() {

    }

    public static function button() {

    }

    public static function submit() {

    }

    public function __toString() {
        $ret = '';

        $attr = array();

        if (sizeof($this->cssClass)) {
            $class = 'class="' . implode(' ', array_unique($this->cssClass, SORT_STRING)) . '"';
            $attr[] = $class;
        }

        if ($this->cssStyle != '') {
            $style = 'style="' . $this->cssStyle . '"';
            $attr[] = $style;
        }

        if ($this->autocomplete === false) {
            $attr[] = 'autocomplete="off"';
        }

        if ($this->disabled === true) {
            $attr[] = 'disabled';
        }

        switch ($this->type) {
            case 'text':
                $ret .= '<input type="text" name="' . $this->name . '" value="' . $this->value . '" ' . implode(' ', $attr) . '>';
                break;
            case 'password':
                $ret .= '<input type="password" name="' . $this->name . '" value="' . $this->value . '" ' . implode(' ', $attr) . '>';
                break;
        }
        return $ret;
    }

    public function setSize($size) {
        $this->size = $size;
        return $this;
    }

    public function setDisabled($disabled) {
        $this->disabled = (bool) $disabled;
        return $this;
    }

    public function setPlaceholder($placeholder) {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function setAutocomplete($autocomplete) {
        $this->autocomplete = (bool) $autocomplete;
        return $this;
    }

    public function addCssClass() {
        for ($i = 0; $i < func_num_args(); $i++) {
            $this->cssClass[] = func_get_arg($i);
        }
        return $this;
    }

    public function removeCssClass($cssClass) {
        $this->cssClass = array_diff($this->cssClass, array($cssClass));
        return $this;
    }

    public function setCssStyle($cssStyle) {
        $this->cssStyle = $cssStyle;
        return $this;
    }

    public function clearCssStyle() {
        $this->cssStyle = '';
    }

    public function getValue() {
        return $this->value;
    }

    public function addValidator(Validator $validator) {
        $this->validators[] = $validator;
        return $this;
    }

    public function validate() {
        $ret = true;
        foreach ($this->validators as $validator) {
            $ret = $ret && $validator->validate($this);
        }
        return $ret;
    }

}
