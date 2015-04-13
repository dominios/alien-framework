<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

/**
 * Class Radio, represents HTML input type radio
 * @package Alien\Forms\Input
 */
class Radio extends Input {

    /**
     * @var Option[]
     */
    protected $options;

    /**
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value) {
        parent::__construct($name, 'radio', $value);
    }

    /**
     * @param Option $option
     * @return $this
     */
    public function addOption(Option $option) {
        $this->options[] = $option;
        return $this;
    }

    /**
     * @param Option $option
     * @return $this
     */
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