<?php

use Alien\Constraint\TypeOf;

class TypeOfTest extends PHPUnit_Framework_TestCase
{

    public function testTypeOfSuccess ()
    {
        $constraint = new TypeOf("\Alien\Constraint\TypeOf");
        $instance = new TypeOf("foo");
        $this->assertEquals(true, $constraint->validate($instance));
    }

    /**
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testTypeOfFailure ()
    {
        $constraint = new TypeOf("\Alien\Constraint\TypeOf");
        $instance = new stdClass();
        $constraint->validate($instance);
    }
}