<?php

namespace Alien\Form\Validator;

use Alien\Form\Input;

interface ValidatorInterface {

    public function validate(Input $input);

}