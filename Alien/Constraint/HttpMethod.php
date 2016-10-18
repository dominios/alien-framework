<?php

namespace Alien\Constraint;

use Alien\Constraint\Exception\InvalidArgumentException;
use Alien\Constraint\Exception\ValidationException;
use Alien\Routing\HttpRequest;

class HttpMethod implements ConstraintInterface
{

    /**
     * List of allowed HTTP methods
     * @var string[]
     */
    protected $requiredMethod = [];


    /**
     * HttpMethod Constraint constructor.
     * @param string ...$requiredMethod list of allowed HTTP methods.
     */
    public function __construct($method, ...$list)
    {
        foreach (func_get_args() as $arg) {
            $this->requiredMethod[] = $arg;
        }
    }

    /**
     * Perform validation upon given value.
     * @param HttpRequest $value tested value.
     * @return bool <code>true</code> on success.
     * @throws InvalidArgumentException when no HttpRequest instance given.
     * @throws ValidationException when request does not match allowed method.
     */
    public function validate($value)
    {
        if (!($value instanceof HttpRequest)) {
            throw new InvalidArgumentException("Given value is not a valid HttpRequest instance");
        }
        if (!in_array($value->getMethod(), $this->requiredMethod)) {
            throw new ValidationException("Request does not match allowed method");
        }
        return true;
    }
}