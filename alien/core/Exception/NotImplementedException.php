<?php

namespace Alien\Exception;

use RuntimeException;

/**
 * Thrown when using not implemented features.
 *
 * All those features will be implemented in future versions or marked as deprecated and deleted.
 *
 * @package Alien\Exception
 */
class NotImplementedException extends RuntimeException
{

}