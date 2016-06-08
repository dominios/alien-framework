<?php

namespace Alien\Constraint;

use Alien\Constraint\Exception\ValidationException;

/**
 * Validates object for instance of exact class.
 */
class TypeOf extends Type
{

    private $type;

    public function __construct($type)
    {
        parent::__construct(Type::TYPE_OBJECT);
        $this->type = $type;
    }

    public function validate($value)
    {
        parent::validate($value);
        if (!is_a($value, $this->type, false)) {
            throw new ValidationException(sprintf('Object must be of type %s.', $this->type));
        }
        return true;
    }
}