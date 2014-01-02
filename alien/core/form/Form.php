<?php

namespace Alien\Forms;

class Form {

    protected $method;
    protected $action;
    protected $id;
    protected $name;
    private $elements = array();

    /**
     * @param string $method
     * @param string $action
     * @param string $name
     */
    public function __construct($method = 'post', $action = '', $name = '') {
        $this->method = strtoupper($method);
        $this->name = $name;
        $this->action = $action;
    }

    public function startTag() {
        $attr = array();
        $attr[] = 'method="' . $this->method . '"';
        $attr[] = 'action="' . $this->action . '"';
        if ($this->id !== '') {
            $attr[] = 'id="' . $this->id . '"';
        }
        return '<form ' . implode(' ', $attr) . '>';
    }

    public function endTag() {
        return '</form>';
    }

    public function isPostSubmit() {
        if ($this->method == 'POST' && sizeof($_POST)) {
            return true;
        } else {
            return false;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getName() {
        return $this->name;
    }

    public function addElement(Input $element) {
        $this->elements[] = $element;
        return $this;
    }

    public function getElement($name) {
        foreach ($this->elements as $elem) {
            if ($elem->getName() === $name) {
                return $elem;
            }
        }
        return false;
    }

    public function validate() {
        $ret = true;
        foreach ($this->elements as $e) {
            $ret &= $e->validate();
        }
        return $ret;
    }

    public function getAction() {
        return $this->action;
    }

    public function setAction($action) {
        $this->action = $action;
        return $this;
    }

    public function setMethod($method) {
        $this->method = strtoupper($method);
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

}
