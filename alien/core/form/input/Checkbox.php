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

    protected function hydrate(){
        if(array_key_exists($this->name, $_POST)){
            $this->value = true;
        } else {
            $this->value = false;
        }
    }

    public function setChecked($checked) {
        $this->checked = (bool) $checked;
    }

    public function isChecked() {
        return (bool) $this->checked;
    }


}