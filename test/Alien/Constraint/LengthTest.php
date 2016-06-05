<?php

use Alien\Constraint\Length;

class LengthTest extends PHPUnit_Framework_TestCase
{
    public function testNoConstraint () {
        $lengthConstraint = new Length();
        $this->assertEquals(true, $lengthConstraint->validate('foo'));
    }

    public function testMinimumLengthSuccess () {
        $lengthConstraint = new Length(2);
        $this->assertEquals(true, $lengthConstraint->validate('foo'));
    }

    /**
     * @expectedException Alien\Constraint\Exception\ValidationException
     */
    public function testMinimumLengthFailure () {
        $lengthConstraint = new Length(5);
        $this->assertEquals(true, $lengthConstraint->validate('foo'));
    }

    public function testMaximumLengthSuccess () {
        $lengthConstraint = new Length(0, 5);
        $this->assertEquals(true, $lengthConstraint->validate('foo'));
    }

    /**
     * @expectedException Alien\Constraint\Exception\ValidationException
     */
    public function testMaximumLengthError () {
        $lengthConstraint = new Length(0, 2);
        $lengthConstraint->validate('foo');
    }

    public function testMixedSuccess () {
        $lengthConstraint = new Length(1, 3);
        $this->assertEquals(true, $lengthConstraint->validate('foo'));
    }

    /**
     * @expectedException Alien\Constraint\Exception\ValidationException
     */
    public function testMixedShortValue () {
        $lengthConstraint = new Length(4, 5);
        $lengthConstraint->validate('foo');
    }

    /**
     * @expectedException Alien\Constraint\Exception\ValidationException
     */
    public function testMixedLongValue () {
        $lengthConstraint = new Length(1, 2);
        $lengthConstraint->validate('foo');
    }

}