<?php

namespace Alien\Constraint;
use Alien\Constraint\Exception\ValidationException;

/**
 * Validates string against given minimum and maximum length.
 */
class Length implements ConstraintInterface
{

    /**
     * @var int minimum required length.
     */
    private $min;

    /**
     * @var int maximum possible length.
     */
    private $max;

    /**
     * Constructs new instance of length constraint.
     * @param int $min [optional] minimum length (defaults: <code>0</code>).
     * @param int $max [optional] maximum length (default: <code>INF</code>).
     */
    public function __construct($min = 0, $max = INF)
    {
        $this->min = $min;
        $this->max = $max;

    }

    public function validate($value)
    {
        if (!is_null($this->min) && strlen($value) < $this->min) {
            throw new ValidationException("Entered value is shorter then minimum.");
        }
        if (!is_null($this->max) && strlen($value) > $this->max) {
            throw new ValidationException("Entered value is longer then maximum.");
        }
        return true;
    }

}