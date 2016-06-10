<?php

use Alien\Constraint\Type;

class TypeTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider stringSuccessValuesProvider
     */
    public function testStringSuccess($string)
    {
        $constraint = new Type(Type::TYPE_STRING);
        $this->assertEquals(true, $constraint->validate($string));
    }

    /**
     * @dataProvider stringFailureValuesProvider
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testStringFailure($nonString)
    {
        $constraint = new Type(Type::TYPE_STRING);
        $constraint->validate($nonString);
    }

    public function stringSuccessValuesProvider()
    {
        return [
            [''], ['foo'], ['123'], ['\n'], ["\n"], ["null"]
        ];
    }

    public function stringFailureValuesProvider()
    {
        return [
            [123], [null], [[]], [new stdClass()], [true], [false], [function () {}]
        ];
    }

    /**
     * @dataProvider numberSuccessValuesProvider
     */
    public function testNumberSuccess($number)
    {
        $constraint = new Type(Type::TYPE_NUMBER);
        $this->assertEquals(true, $constraint->validate($number));
    }

    /**
     * @dataProvider numberFailureValuesProvider
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testNumberFailure($nonNumber)
    {
        $constraint = new Type(Type::TYPE_NUMBER);
        $constraint->validate($nonNumber);
    }

    public function numberSuccessValuesProvider ()
    {
        return [
            [1], [1.1], ["123"], ["3.6"], [0123], [1337e0], [0b10100111001]
        ];
    }

    public function numberFailureValuesProvider ()
    {
        return [
            [null], [true], [false], [[]], [new stdClass()], [function() {}], ["a123"], ["123a"], ["3,5"]
        ];
    }

    public function testIntegerSuccess ()
    {
        $constraint = new Type(Type::TYPE_INTEGER);
        $this->assertEquals(true, $constraint->validate(1));
    }

    /**
     * @dataProvider integerFailureValuesProvider
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testIntegerFailure ($nonInteger)
    {
        $constraint = new Type(Type::TYPE_INTEGER);
        $constraint->validate($nonInteger);
    }

    public function integerFailureValuesProvider ()
    {
        return [
            [null], [true], [false], [[]], [new stdClass()], [function() {}], ["a123"], ["123a"], ["3,5"], [1.1], ["123"], ["3.6"], [1337e0]
        ];
    }

    /**
     * @dataProvider floatSuccessValuesProvider
     */
    public function testFloatSuccess ($float)
    {
        $constraint = new Type(Type::TYPE_FLOAT);
        $this->assertEquals(true, $constraint->validate($float));
    }

    /**
     * @dataProvider floatFailureValuesProvider
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testFloatFailure($nonFloat)
    {
        $constraint = new Type(Type::TYPE_FLOAT);
        $constraint->validate($nonFloat);
    }

    public function floatSuccessValuesProvider ()
    {
        return [
            [1.1], [.2], [1337e0]
        ];
    }

    public function floatFailureValuesProvider ()
    {
        return [
            [null], [true], [false], [[]], [new stdClass()], [function() {}], ["a123"], ["123a"], ["3,5"], [1], ["123"], ["3.6"]
        ];
    }

    public function testBoolSuccess ()
    {
        $constraint = new Type(Type::TYPE_BOOL);
        $this->assertEquals(true, $constraint->validate(true));
        $this->assertEquals(true, $constraint->validate(false));
    }

    /**
     * @dataProvider boolFailureValuesProvider
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testBoolFailure ($nonBool)
    {
        $constraint = new Type(Type::TYPE_BOOL);
        $constraint->validate($nonBool);
    }

    public function boolFailureValuesProvider ()
    {
        return [
            [null], [[]], [new stdClass()], [function() {}], ["a123"], ["123a"], ["3,5"], [1], ["123"], ["3.6"], [1.1], [.2], [1337e0]
        ];
    }

    public function testArraySuccess ()
    {
        $constraint = new Type(Type::TYPE_ARRAY);
        $this->assertEquals(true, $constraint->validate(array()));
        $this->assertEquals(true, $constraint->validate([]));
    }

    /**
     * @dataProvider arrayFailureValuesProvider
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testArrayFailure ($nonArray)
    {
        $constraint = new Type(Type::TYPE_ARRAY);
        $constraint->validate($nonArray);
    }

    public function arrayFailureValuesProvider ()
    {
        return [
            [true], [false], [null], [new stdClass()], [function() {}], ["a123"], ["123a"], ["3,5"], [1], ["123"], ["3.6"], [1.1], [.2], [1337e0]
        ];
    }

    public function testObjectSuccess ()
    {
        $constraint = new Type(Type::TYPE_OBJECT);
        $this->assertEquals(true, $constraint->validate(new stdClass()));
        $this->assertEquals(true, $constraint->validate(function () {}));
        $this->assertEquals(true, $constraint->validate(new Type(Type::TYPE_OBJECT)));
    }

    /**
     * @dataProvider objectFailureValuesProvider
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testObjectFailure ($nonObject)
    {
        $constraint = new Type(Type::TYPE_OBJECT);
        $constraint->validate($nonObject);
    }

    public function objectFailureValuesProvider ()
    {
        return [
            [[]], [array()], [true], [false], [null], ["a123"], ["123a"], ["3,5"], [1], ["123"], ["3.6"], [1.1], [.2], [1337e0]
        ];
    }

}