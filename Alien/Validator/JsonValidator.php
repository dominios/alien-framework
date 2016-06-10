<?php

namespace Alien\Validator;

use Alien\Constraint\Exception\ValidationException;
use Alien\Constraint\Type;
use Alien\Constraint\TypeOf;

class JsonValidator extends Type
{

    protected $ruleSet;

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
        foreach ($this->ruleSet as $property => $constraint) {
            if (is_array($constraint)) {
                foreach ($constraint as $c) {
                    $c->validate(@$json[$property] ?: null);
                }
            } else {
                $constraint->validate(@$json[$property] ?: null);
            }
        }
        return true;
    }
}