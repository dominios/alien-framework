<?php

namespace Alien\Constraint;

use Alien\Constraint\Exception\ValidationException;

/**
 * Validates string against regular expression.
 */
class Regex implements ConstraintInterface
{

    /**
     * @var string pattern to validate against.
     */
    private $pattern;

    /**
     * @var string pattern modifiers.
     */
    private $modifiers;

    /**
     * Creates new instance of regex constraint.
     *
     * Pass regular expression without delimiters and
     * modifiers as separate second optional argument.
     *
     * <b>WARNING</b>: regular expression is not escaped!
     *
     * @param string $pattern regular expression pattern.
     * @param string $modifiers [optional] regular expression modifiers.
     */
    public function __construct($pattern, $modifiers = '')
    {
        $this->pattern = $pattern;
        $this->modifiers = $modifiers;
    }

    public function validate($value)
    {
        if (!preg_match("~" . $this->pattern . "~" . (string)$this->modifiers, $value)) {
            throw new ValidationException('Entered value does not match regular expression.');
        }
        return true;
    }

}