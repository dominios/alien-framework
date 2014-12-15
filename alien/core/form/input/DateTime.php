<?php

namespace Alien\Forms\Input;

use Alien\Forms\Input;
use DateTime;

class DateTimeLocal extends Input {

    /**
     * @var DateTime
     */
    protected $value;

    /**
     * @var DateTime
     */
    protected $defaultValue;

    public function __construct($name, DateTime $defaultValue = null, DateTime $value = null) {
        parent::__construct($name, 'datetime-local', $defaultValue, $value);
    }

    public function __toString() {
        $ret = '';
        $value = $this->value instanceof DateTime ? preg_replace('/[\+\-]\d\d:\d\d$/', '', $this->value->format(DateTime::RFC3339)) : "";
        $ret .= '<input type="datetime-local" name="' . $this->name . '" value="' . $value . '">';
        return $ret;
    }

}