<?php

use Alien\Constraint\Regex;

class RegexTest extends PHPUnit_Framework_TestCase
{

    public function testRegexSuccess () {
        $regexConstraint = new Regex('^\d[a-z]+x$');
        $this->assertEquals(true, $regexConstraint->validate('1abcx'));
    }

    /**
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testRegexFailure () {
        $regexConstraint = new Regex('^\d[a-z]+x$');
        $this->assertEquals(true, $regexConstraint->validate('1aBcX'));
    }

    public function testRegexSuccessWithModifiers () {
        $regexConstraint = new Regex('^\d[a-z]+x$', 'i');
        $this->assertEquals(true, $regexConstraint->validate('1aBcX'));
    }

}