<?php

namespace Alien\Forms;

use Alien\View;
use InvalidArgumentException;
use Iterator;
use Countable;
use SeekableIterator;
use OutOfBoundsException;

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
     * @param String $name
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
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return Input current Input field
     */
    public function current() {
        return $this->fields[$this->position];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next() {
        $this->position++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key() {
        return $this->position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid() {
        return $this->position < count($this->fields) ? true : false;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count() {
        return (int) count($this->fields);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Seeks to a position
     * @link http://php.net/manual/en/seekableiterator.seek.php
     * @param int $position <p>
     * The position to seek to.
     * </p>
     * @return void
     */
    public function seek($position) {
        if (!isset($this->fields[$position])) {
            throw new OutOfBoundsException();
        }
        $this->position = $position;
    }

    public function push(Input $field) {
        array_push($this->fields, $field);
    }

    public function pop() {
        return array_pop($this->fields);
    }

    /**
     * @return String Fieldset name
     */
    public function getName() {
        return $this->name;
    }

    public function __toString() {
        $view = new View(strlen($this->getViewSrc()) ? $this->getViewSrc() : Fieldset::DEFAULT_VIEW);
        $view->fields = $this;
        return $view->__toString();
    }

    public function setViewSrc($viewSrc) {
        $this->viewSrc = $viewSrc;
        return $this;
    }

    /**
     * @return String
     */
    public function getViewSrc() {
        return $this->viewSrc;
    }


}