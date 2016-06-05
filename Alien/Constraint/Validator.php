<?php

namespace Alien\Constraint;

/**
 * Handles validation process.
 */
class Validator
{

    /**
     * @var bool set tu <code>true</code> to stop validation on first failure.
     */
    private $breakOnFailure = true;

    /**
     * @todo
     * @param $input
     */
    public function validate($input)
    {

    }

    /**
     * Sets behaviour on first failure.
     * @param bool $breakOnFailure set to <code>true</code> to stop validation on first failure.
     * @return Validator validator instance.
     */
    public function setBreakOnFailure($breakOnFailure)
    {
        $this->breakOnFailure = $breakOnFailure;
        return $this;
    }

    /**
     * Returns if should stop validating in first failure.
     * @return boolean
     */
    public function isBreakingOnFailure()
    {
        return $this->breakOnFailure;
    }


}

