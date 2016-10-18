<?php

use Alien\Constraint\HttpMethod;
use Alien\Routing\HttpRequest;

class HttpMethodTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @testdox constructor should accept single argument.
     */
    public function shouldAcceptSingleMethod()
    {
        $constraint = new HttpMethod('GET');
        $request = HttpRequest::createFromString('GET /uri HTTP/1.1');
        $this->assertEquals(true, $constraint->validate($request));
    }

    /**
     * @test
     * @testdox constructor should accept multiple arguments.
     */
    public function shouldAcceptMultipleMethods()
    {
        $constraint = new HttpMethod('GET', 'POST', 'PUT');
        $request1 = HttpRequest::createFromString('GET /uri HTTP/1.1');
        $request2 = HttpRequest::createFromString('POST /uri HTTP/1.1');
        $request3 = HttpRequest::createFromString('PUT /uri HTTP/1.1');
        $this->assertEquals(true, $constraint->validate($request1));
        $this->assertEquals(true, $constraint->validate($request2));
        $this->assertEquals(true, $constraint->validate($request3));
    }

    /**
     * @test
     * @testdox validation should throw an exception on mismatch.
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function shouldThrowExceptionOnFailure()
    {
        $constraint = new HttpMethod('GET');
        $request = HttpRequest::createFromString('DELETE /uri HTTP/1.1');
        $this->assertEquals(true, $constraint->validate($request));
    }

}