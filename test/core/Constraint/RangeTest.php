<?php

use Alien\Constraint\Range;

class RangeTest extends PHPUnit_Framework_TestCase
{

    public function testMinSuccess()
    {
        $rangeConstraint = new Range(1);
        $this->assertEquals(true, $rangeConstraint->validate(1.1));
    }

    public function testMaxSuccess()
    {
        $rangeConstraint = new Range(0, 10);
        $this->assertEquals(true, $rangeConstraint->validate(5));
    }

    public function testMinAndMaxSuccess()
    {
        $rangeConstraint = new Range(5, 10);
        $this->assertEquals(true, $rangeConstraint->validate(7));
    }

    /**
     * @expectedException \Alien\Constraint\Exception\RangeException
     */
    public function testMinFailure()
    {
        $rangeConstraint = new Range(0);
        $rangeConstraint->validate(-5);
    }

    /**
     * @expectedException \Alien\Constraint\Exception\RangeException
     */
    public function testMaxFailure()
    {
        $rangeConstraint = new Range(0, 10);
        $rangeConstraint->validate(15);
    }

    /**
     * @expectedException \Alien\Constraint\Exception\RangeException
     */
    public function testMinAndMaxFailure()
    {
        $rangeConstraint = new Range(0, 10);
        $rangeConstraint->validate(100);
    }

    public function testHandlingNumericValue()
    {
        $rangeConstraint = new Range(-5, 5);
        $this->assertEquals(true, $rangeConstraint->validate("2"));
        $this->assertEquals(true, $rangeConstraint->validate("0x539"));
        $this->assertEquals(true, $rangeConstraint->validate("1.34"));
    }

    /**
     * @expectedException \Alien\Constraint\Exception\InvalidArgumentException
     */
    public function testHandlingNonNumericValue()
    {
        $rangeConstraint = new Range(-5, 5);
        $this->assertEquals(true, $rangeConstraint->validate("foo"));
        $this->assertEquals(true, $rangeConstraint->validate([]));
        $this->assertEquals(true, $rangeConstraint->validate(new stdClass()));
    }

}