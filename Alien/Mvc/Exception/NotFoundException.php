<?php

namespace Alien\Mvc\Exception;

use BadMethodCallException;

/**
 * Thrown when calling undefined controller's action
 * @package Alien\Mvc\Exception
 */
class NotFoundException extends BadMethodCallException
{

}