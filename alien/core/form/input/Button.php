<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;

class Button extends Input {

    public function __construct($action, $text, $icon = null) {
        parent::__construct('', 'button', null, $action, null);
        $this->icon = $icon;
        $this->placeholder = $text;
        $this->addCssClass('button');
    }

    public function __toString() {
        $attr = $this->commonRenderAttributes();
        $ret = '';
        $icon = strlen($this->icon) ? '<span class="icon ' . $this->icon . ($this->disabled ? '' : '-light') . '"></span>' : '';
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