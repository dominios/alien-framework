<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

class Checkbox extends Input {

    protected $checked = false;

    public function __construct($name, $value, $checked = false) {
        parent::__construct($name, 'checkbox', $value);
        $this->checked = $checked;
    }

    public function __toString() {
        $ret = '';
        $ret .= '<input type="checkbox" name="' . $this->name . '" value="' . $this->value . '" ' . ($this->isChecked() ? 'checked' : '') . '>';
        return $ret;
    }

    public function hydrate() {
        parent::hydrate();
        if (is_array(self::$hydratorArray)) {
            array_key_exists($this->name, self::$hydratorArray) ? $this->setChecked(true) : $this->setChecked(false);
        }
        return $this;
    }

    public function setChecked($checked) {
        $this->checked = (bool) $checked;
    }

    public function isChecked() {
        return (bool) $this->checked;
    }


}