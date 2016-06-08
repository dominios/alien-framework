<?php

use Alien\Constraint\Required;

class RequiredTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Required
     */
    private $constraint;

    public function setUp()
    {
        $this->constraint = new Required();
    }

    public function testNonEmptyValue() {
        $this->assertEquals(true, $this->constraint->validate('foo'));
        $this->assertEquals(true, $this->constraint->validate('123'));
        $this->assertEquals(true, $this->constraint->validate(0));
        $this->assertEquals(true, $this->constraint->validate(0.0));
        $this->assertEquals(true, $this->constraint->validate(123));
        $this->assertEquals(true, $this->constraint->validate('null'));
        $this->assertEquals(true, $this->constraint->validate(true));
        $this->assertEquals(true, $this->constraint->validate(false));
        $this->assertEquals(true, $this->constraint->validate(new stdClass()));
        $this->assertEquals(true, $this->constraint->validate(['foo']));
        // used simple string: \n has no special meaning in this case
        $this->assertEquals(true, $this->constraint->validate('\n'));
    }

    /**
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testEmptyArrayShouldFail() {
        $this->assertEquals(true, $this->constraint->validate([]));
    }

    /**
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testNullShouldFail() {
        $this->assertEquals(true, $this->constraint->validate(null));
    }

    /**
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testEmptyStringShouldFail() {
        $this->assertEquals(true, $this->constraint->validate(""));
        $this->assertEquals(true, $this->constraint->validate(" "));
    }

    /**
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testStringShouldBeTrimmedBeforeValidating() {
        $this->assertEquals(true, $this->constraint->validate(" "));
    }

    /**
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testWhiteCharactersHandledAsEmpty() {
        $this->assertEquals(true, $this->constraint->validate("\n"));
    }
}