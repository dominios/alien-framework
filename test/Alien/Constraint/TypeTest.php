<?php

use Alien\Constraint\Type;

class TypeTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider stringSuccessValuesProvider
     * @param $string string tested value which should pass
     */
    public function testStringSuccess($string)
    {
        $constraint = new Type(Type::TYPE_STRING);
        $this->assertEquals(true, $constraint->validate($string));
    }

    /**
     * @dataProvider stringFailureValuesProvider
     * @expectedException \Alien\Constraint\Exception\ValidationException
     * @param $string string tested value which should fail and throw an exception
     */
    public function testStringFailure($string)
    {
        $constraint = new Type(Type::TYPE_STRING);
        $constraint->validate($string);
    }

    public function stringSuccessValuesProvider() {
        return [
            [''], ['foo'], ['123'], ['\n'], ["\n"], ["null"]
        ];
    }

    public function stringFailureValuesProvider() {
        return [
            [123], [null], [[]], [new stdClass()], [true], [false], [function () {}]
        ];
    }

}