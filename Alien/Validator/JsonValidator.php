<?php

namespace Alien\Validator;

use Alien\Constraint\ConstraintInterface;
use Alien\Constraint\Exception\ValidationException;
use Alien\Constraint\Type;
use Alien\Constraint\TypeOf;

class JsonValidator extends Type
{

    /**
     * Set of rules for validation.
     * The structure is same as target JSON but at leafs level
     * there can be either single constraint or array of constraints.
     * @var array
     */
    protected $ruleSet;

    /**
     * JsonValidator constructor.
     * @param array $ruleSet set of rules for JSON.
     */
    public function __construct (array $ruleSet)
    {
        parent::__construct(TypeOf::TYPE_ARRAY);
        $this->ruleSet = $ruleSet;
    }

    /**
     * Perform validation upon given json.
     * @param array $json tested array.
     * @return bool <code>true</code> on success.
     * @throws ValidationException on validation failure.
     */
    public function validate ($json)
    {

        $ret = true;
        foreach ($this->ruleSet as $key => $value) {
            $ret &= $this->resolveRule($json, $key, $value);
        }
        return $ret;
    }

    /**
     * Resolves current JSON level.
     * If there is specific constraint given, it's automatically applied to value at JSON's key.
     * If instead of single constraint array is given, the value of key in JSON is tested against
     * each of the constraint.
     * If none of above is true, it's considered as next-level of the JSON and the validation
     * repeats recursively.
     * @param array $json JSON to validate.
     * @param string $key current key which is validate.
     * @param ConstraintInterface|ConstraintInterface[]|array $constraint single constraint, collection of constraints or next JSON level rule set.
     * @return bool true on success
     * @throws ValidationException on validation failure on first occurrence.
     */
    protected function resolveRule ($json, $key, $constraint)
    {
        if ($constraint instanceof ConstraintInterface) {
            return $this->applyConstraint($constraint, @$json[$key] ?: null);
        } else if (is_array($constraint) && sizeof($constraint)) {
            $constraints = $constraint;
            $keys = array_keys($constraints);
            if (is_int($keys[0]) && $constraints[0] instanceof ConstraintInterface) {
                // list of constraints
                // apply all constraints one after other upon same value
                $ret = true;
                foreach ($constraints as $constraint) {
                    $ret &= $this->applyConstraint($constraint, @$json[$key] ?: null);
                }
                return $ret;
            } else {
                // multi level recursion
                // handle next level of the json same way as before recursively
                $ret = true;
                $json = $json[$key];
                foreach ($constraints as $key => $rule) {
                    $ret &= $this->resolveRule($json, $key, $rule);
                }
                return $ret;
            }
        }
    }

    /**
     * Test given value against constraint.
     * @param ConstraintInterface $constraint validation rule.
     * @param mixed $value value to test.
     * @return bool validation result
     * @throws ValidationException on validation failure.
     */
    protected function applyConstraint (ConstraintInterface $constraint, $value)
    {
        return $constraint->validate($value);
    }
}