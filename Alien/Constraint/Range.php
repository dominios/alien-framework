<?php

namespace Alien\Constraint;

use Alien\Constraint\Exception\InvalidArgumentException;
use Alien\Constraint\Exception\RangeException;

/**
 * Validates given number against minimum and maximum value.
 */
class Range implements ConstraintInterface
{

    /**
     * @var float minimum required value.
     */
    private $min;

    /**
     * @var float maximum required value.
     */
    private $max;

    /**
     * Constructs new instance of range constraint.
     * @param float $min [optional] minimum length (defaults: <code>0</code>).
     * @param float $max [optional] maximum length (default: <code>INF</code>).
     */
    public function __construct($min = 0, $max = INF)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Checks if given value is in range.
     * <b>NOTE</b>: given value is compared as <code>float</code>.
     * @param mixed $value value to test.
     * @return bool returns <code>true</code> on success.
     * @throws RangeException on validation error.
     * @throws InvalidArgumentException when non numeric argument given.
     */
    public function validate($value)
    {
        if (is_null($value) || !is_numeric($value)) {
            throw new InvalidArgumentException("Given value is not a number.");
        }
        if ((float) $value < $this->min) {
            throw new RangeException("Given value is lower then minimum.");
        }
        if ((float) $value > $this->max) {
            throw new RangeException("Given value is greater then maximum.");
        }
        return true;
    }

}