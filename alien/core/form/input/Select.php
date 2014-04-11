<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

class Select extends Input {

    protected $options;
    protected $multiple = false;

    public function __construct($name) {
        parent::__construct($name, 'select', '');
    }

    public function addOption(Option $option) {
        $this->options[] = $option;
        return $this;
    }

    public function selectOption(Option $option) {
        $option->setSelected(true);
        return $this;
    }

    public function __toString() {
        $attrs = $this->commonRenderAttributes(true);
        if (!is_null($this->getSize())) {
            $attrs[] = 'size="' . $this->getSize() . '"';
        }
        if ($this->isMultiple()) {
            $attrs[] = 'multiple';
        }
        $ret = '';
        $ret .= '<select name="' . $this->getName() . '" ' . (is_array($attrs) ? implode(' ', $attrs) : '') . '>';
        if (is_array($this->options)) {
            foreach ($this->options as $opt) {
                $ret .= $opt;
            }
        }
        $ret .= '</select>';
        return $ret;
    }

    public function setMultiple($multiple) {
        $this->multiple = $multiple;
        return $this;
    }

    public function isMultiple() {
        return (bool) $this->multiple;
    }

}