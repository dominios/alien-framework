<?php

namespace Alien\Forms;

use Exception;
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

    /**
     * @var string HTML form method attribude
     */
    protected $method;

    /**
     * @var string HTML form action attribute
     */
    protected $action;

    /**
     * @var string HTML DOM id attribute
     */
    protected $id;

    /**
     * @var string form name
     */
    protected $name;

    /**
     * @var Input[] inputs
     */
    private $fields = array();

    /**
     * @var Fieldset[] fieldsets
     */
    private $fieldsets = array();

    /**
     * Constructs new Form instance
     * @param string $method
     * @param string $action
     * @param string $name
     */
    public function __construct($method = 'post', $action = '', $name = '') {
        $this->method = strtoupper($method);
        $this->name = $name;
        $this->action = $action;

        if (Form::AUTO_PREPEND_CSRF && !$this->hasField('csrfToken')) {
            Input::csrf()->addToForm($this);
        }
    }

    /**
     * Returns HTML representation of start tag and possibly CSRF token input
     * @return string
     */
    public function startTag() {
        $attr = array();
        $attr[] = 'method="' . $this->method . '"';
        $attr[] = 'action="' . $this->action . '"';
        if ($this->id !== '') {
            $attr[] = 'id="' . $this->id . '"';
        }
        $ret = '';
        $ret .= '<form ' . implode(' ', $attr) . '>';

        if (Form::AUTO_PREPEND_CSRF_IF_EXISTS && $this->hasField('csrfToken')) {
            $ret .= $this->getField('csrfToken')->__toString();
        }

        return $ret;
    }

    /**
     * Returns HTML form end tag
     * @return string
     */
    public function endTag() {
        return '</form>';
    }

    /**
     * Checks if Form has been submitted
     * @return bool
     */
    public function isPostSubmit() {
        if ($this->method == 'POST' && sizeof($_POST)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns DOM id
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set DOM id
     * @param string $id
     * @return $this
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns Form method, ie GET or POST
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Returns Form name
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Adds Input to form Input fields
     * @param Input $field
     * @return $this
     * @throws \InvalidArgumentException if field is already present in Form
     */
    public function addElement(Input $field) {
        if ($this->hasField($field->getName())) {
            throw new InvalidArgumentException('Cannot add element <b>' . $field->getName() . '</b> to form. Element already exists!');
        }
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Return Input field by name
     * @param string $name needle
     * @param bool $includeFieldsets wheter to continue to search in form's fieldsets when input is not present in form's fields directly
     * @throws \Exception if Input is not found
     * @return Input
     */
    public function getField($name, $includeFieldsets = false) {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return $field;
            }
        }
        if ($includeFieldsets) {
            foreach ($this->fieldsets as $fieldset) {
                try {
                    return $fieldset->getField($name);
                } catch (Exception $e) {
                }
            }
        }
        throw new Exception("Input '$name' not found.");
    }

    /**
     * Returns Fieldset by name
     * @param string $name needle
     * @return Fieldset
     * @throws \Exception if Fieldset is not found
     */
    public function getFieldset($name) {
        foreach ($this->fieldsets as $fieldset) {
            if ($fieldset->getName() == $name) {
                return $fieldset;
            }
        }
        throw new Exception("Fieldset '$name' not found.");
    }

    /**
     * Validate every input in form including inputs in fieldsets
     * @param null|array $hydratorArray array to use for hydrating before validation. If set to null, using $_POST as default
     * @return bool
     */
    public function validate($hydratorArray = null) {
        if ($hydratorArray === null) {
            Input::setHydratorArray($_POST);
        } else {
            Input::setHydratorArray($hydratorArray);
        }
        $ret = true;
        foreach ($this->fields as $e) {
            $e->hydrate();
            $ret &= $e->validate();
        }
        foreach ($this->fieldsets as $fs) {
            foreach ($fs as $e) {
                $e->hydrate();
                $ret &= $e->validate();
            }
        }
        return $ret;
    }

    /**
     * Returns Form action attribute
     * @return string
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * Sets Form action attribute
     * @param string $action
     * @return $this
     */
    public function setAction($action) {
        $this->action = $action;
        return $this;
    }

    /**
     * Sets Form method attribute
     * @param string $method
     * @return $this
     */
    public function setMethod($method) {
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Sets Form name
     * @param string $name
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Checks if Form has specified field by name
     * @param string $name needle
     * @return bool
     */
    public function hasField($name) {
        $has = false;
        foreach ($this->fields as $field) {
            $has |= $field->getName() == $name;
        }
        return $has;
    }

    /**
     * Adds Fieldset to the Form
     * @param Fieldset $fieldset
     * @return $this
     */
    public function addFieldset(Fieldset $fieldset) {
        $this->fieldsets[] = $fieldset;
        return $this;
    }

}
