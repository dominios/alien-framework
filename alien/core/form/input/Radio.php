<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

class Radio extends Input {

    protected $options;

    public function __construct($name, $value) {
        parent::__construct($name, 'radio', $value);
    }

    public function addOption(Option $option) {
        $this->options[] = $option;
        return $this;
    }

    public function checkOption(Option $option) {
        $option->setSelected(true);
        return $this;
    }

    public function __toString() {
        $ret = '';
        foreach ($this->options as $opt) {
            $ret .= $opt;
        }
        return $ret;
    }


}