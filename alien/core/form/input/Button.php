<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

/**
 * Class Button, represents HTML buttons
 * @package Alien\Forms\Input
 */
class Button extends Input {

    /**
     * @param string $action
     * @param string $text
     * @param null|string $icon
     */
    public function __construct($action, $text, $icon = null) {
        parent::__construct('', 'button', null, $action, null);
        $this->icon = $icon;
        $this->placeholder = $text;
        $this->addCssClass('btn');
        $this->addCssClass('btn-default');
    }

    public function __toString() {
        $attr = $this->commonRenderAttributes();
        $ret = '';
        $icon = strlen($this->icon) ? '<i class="' . $this->icon . '"></i> ' : '';
        if (preg_match('/javascript/', $this->value)) {
            $attr[] = 'onClick="' . ($this->disabled ? 'javascript: return false;' : $this->value) . '"';
            $attr[] = 'href="#"';
        } else {
            $attr[] = 'href="' . ($this->disabled ? '#' : $this->value) . '"';
        }
        $ret .= '<a ' . implode(' ', $attr) . '>' . $icon . $this->placeholder . '</a>';
        return $ret;
    }

}