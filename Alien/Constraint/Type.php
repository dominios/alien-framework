<?php

namespace Alien\Constraint;

use Alien\Constraint\Exception\ValidationException;

/**
 * Validates given value for one of the scalar types.
 *
 * <b>NOTE:</b> php native <code>is_*<code> functions are used.
 *
 * <b>NOTE:</b> when checking against type <code>Object</code> callable functions will pass
 * because they are represented as <code>Closure</code> objects.
 */
class Type implements ConstraintInterface
{

    const TYPE_STRING = "string";
    const TYPE_NUMBER = "number";
    const TYPE_INTEGER = "int";
    const TYPE_FLOAT = "float";
    const TYPE_BOOL = "bool";
    const TYPE_ARRAY = "array";
    const TYPE_OBJECT = "object";

    private $requiredType;

    public function __construct($requiredType)
    {
        $this->requiredType = $requiredType;
    }

    public function validate($value) {
        if ($this->requiredType === self::TYPE_STRING && !is_string($value)) {
            throw new ValidationException("Given value must be of type string.");
        }
        if ($this->requiredType === self::TYPE_NUMBER && !is_numeric($value)) {
            throw new ValidationException("Given value must be either int or float.");
        }
        if ($this->requiredType === self::TYPE_INTEGER && !is_int($value)) {
            throw new ValidationException("Given value must be of type int.");
        }
        if ($this->requiredType === self::TYPE_FLOAT && !is_float($value)) {
            throw new ValidationException("Given value must be of type float.");
        }
        if ($this->requiredType === self::TYPE_BOOL && !is_bool($value)) {
            throw new ValidationException("Given value must be of type bool.");
        }
        if ($this->requiredType === self::TYPE_ARRAY && !is_array($value)) {
            throw new ValidationException("Given value must be array.");
        }
        if ($this->requiredType === self::TYPE_OBJECT && !is_object($value)) {
            throw new ValidationException("Given value must be object.");
        }
        return true;
    }

}