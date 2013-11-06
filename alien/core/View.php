<?php

namespace Alien;

use Alien\Controllers\BaseController;

class View {

    private $script;
    private $controller;
    private $data = array();
    private $autoEscape = true;
    private $autoStripTags = false;

    public function __construct($script, BaseController $controller = null) {
        $this->script = $script;
        $this->controller = $controller;
    }

    public function renderToString() {
        $content = '';
        if (file_exists($this->script)) {
            ob_start();
            include $this->script;
            $content .= ob_get_contents();
            ob_end_clean();
        } else {
//            Terminal::getInstance()->putMessage('Cannot open view <i>' . $this->script . '</i>', Terminal::CONSOLE_WARNING);
        }
        return $content;
    }

    public function getController() {
        return $this->controller;
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name) {
        if (isset($this->data[$name])) {
            return $this->autoEscape && is_string($this->data[$name]) ? $this->escapeValue($this->data[$name]) : $this->data[$name];
        } else {
            return null;
        }
    }

    private function escapeValue($value, $stripTags = null, $allowedTags = '') {
        if (!is_string($value)) {
            return $value;
        }
        $value .= "";
        if (is_null($stripTags)) {
            $stripTags = $this->autoStripTags;
        }
        if ($stripTags) {
            $value = strip_tags($value, $allowedTags);
        }
        return htmlentities($value, ENT_COMPAT, 'UTF-8');
    }

    public function setAutoEscape($escape = true) {
        $this->autoEscape = (bool) $escape;
    }

    public function setAutoStripTags($stripTags = false) {
        $this->autoStripTags = (bool) $stripTags;
    }

    public function escaped($value, $stripTags = null, $allowedTags = '') {
        return is_string($value) ? $this->escapeValue($this->data[$value], (bool) $stripTags, (string) $allowedTags) : $value;
    }

}
