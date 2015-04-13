<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

/**
 * Class Select, represents HTML input type select
 * @package Alien\Forms\Input
 */
class Select extends Input {

    /**
     * @var Option[]
     */
    protected $options;

    /**
     * @var bool
     */
    protected $multiple = false;

    /**
     * @param string $name
     */
    public function __construct($name) {
        parent::__construct($name, 'select', '');
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
    public function selectOption(Option $option) {
        $option->setSelected(true);
        return $this;
    }

    public function __toString() {
        $this->addCssClass('form-control');
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

    /**
     * @param bool $multiple
     * @return $this
     */
    public function setMultiple($multiple) {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMultiple() {
        return (bool) $this->multiple;
    }

}