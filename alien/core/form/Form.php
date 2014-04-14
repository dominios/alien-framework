<?php

namespace Alien\Forms;

use InvalidArgumentException;

class Form {

    /**
     * Automatically add CSRF Input after form start tag
     */
    const AUTO_PREPEND_CSRF_IF_EXISTS = true;

    /**
     * Automatically create and add CSRF Input to form
     */
    const AUTO_PREPEND_CSRF = true;

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

        if (Form::AUTO_PREPEND_CSRF && !$this->hasElement('csrfToken')) {
            Input::csrf()->addToForm($this);
        }
    }

    public function startTag() {
        $attr = array();
        $attr[] = 'method="' . $this->method . '"';
        $attr[] = 'action="' . $this->action . '"';
        if ($this->id !== '') {
            $attr[] = 'id="' . $this->id . '"';
        }
        $ret = '';
        $ret .= '<form ' . implode(' ', $attr) . '>';

        if (Form::AUTO_PREPEND_CSRF_IF_EXISTS && $this->hasElement('csrfToken')) {
            $ret .= $this->getElement('csrfToken')->__toString();
        }

        return $ret;
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
        if ($this->hasElement($element->getName())) {
            throw new InvalidArgumentException('Cannot add element <b>' . $element->getName() . '</b> to form. Element already exists!');
        }
        $this->elements[] = $element;
        return $this;
    }

    /**
     *
     * @param string $name
     * @return Input
     */
    public function getElement($name) {
        foreach ($this->elements as $elem) {
            if ($elem->getName() === $name) {
                return $elem;
            }
        }
        return false;
    }

    public function validate($hydratorArray = null) {
        if ($hydratorArray === null) {
            Input::setHydratorArray($_POST);
        } else {
            Input::setHydratorArray($hydratorArray);
        }
        $ret = true;
        foreach ($this->elements as $e) {
            $e->hydrate();
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

    public function hasElement($name) {
        $has = false;
        foreach ($this->elements as $input) {
            $has |= $input->getName() == $name;
        }
        return $has;
    }
}
