<?php

namespace Alien\Form;

use Alien\View;
use Exception;
use InvalidArgumentException;
use Iterator;
use Countable;
use SeekableIterator;
use OutOfBoundsException;

/**
 * Class Fieldset
 * @package Alien\Forms
 */
class Fieldset implements Iterator, Countable, SeekableIterator {

    const DEFAULT_VIEW = 'display/common/fieldset.php';

    /**
     * @var String Fieldset name
     */
    protected $name;
    /**
     * @var Input[] array of all Input fields in fieldset
     */
    protected $fields;
    /**
     * @var int current position
     */
    protected $position;
    /**
     * @var String view script source destination
     */
    protected $viewSrc;

    /**
     * @param string $name
     * @throws InvalidArgumentException
     */
    public function __construct($name) {
        if (!strlen($name)) {
            throw new InvalidArgumentException("Fieldset name must be valid string.");
        }
        $this->name = $name;
        $this->fields = array();
        $this->position = 0;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return Input current Input field
     */
    public function current() {
        return $this->fields[$this->position];
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next() {
        $this->position++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key() {
        return $this->position;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid() {
        return $this->position < count($this->fields) ? true : false;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * The return value is cast to an integer.
     */
    public function count() {
        return (int) count($this->fields);
    }

    /**
     * Seeks to a position
     * @link http://php.net/manual/en/seekableiterator.seek.php
     * @param int $position
     * The position to seek to.
     * @throws \OutOfBoundsException
     * @return void
     */
    public function seek($position) {
        if (!isset($this->fields[$position])) {
            throw new OutOfBoundsException();
        }
        $this->position = $position;
    }

    /**
     * Push element to fieldset
     * @param Input $field
     */
    public function push(Input $field) {
        array_push($this->fields, $field);
    }

    /**
     * Returns the last element in fieldset, or NULL if fieldset is empty
     * @return mixed
     */
    public function pop() {
        return array_pop($this->fields);
    }

    /**
     * @return String Fieldset name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Renders fieldset into string
     * @return string
     */
    public function __toString() {
        $view = new View(strlen($this->getViewSrc()) ? $this->getViewSrc() : Fieldset::DEFAULT_VIEW);
        $view->fields = $this;
        return $view->__toString();
    }

    /**
     * Sets path to HTML template
     * @param string $viewSrc
     * @return $this
     */
    public function setViewSrc($viewSrc) {
        $this->viewSrc = $viewSrc;
        return $this;
    }

    /**
     * @return string
     */
    public function getViewSrc() {
        return $this->viewSrc;
    }

    /**
     * Returns input with given name. Throws Exception if no element is found.
     * @param string $name
     * @return Input
     * @throws \Exception
     */
    public function getField($name) {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return $field;
            }
        }
        throw new Exception("Input '$name' not found.");
    }

}