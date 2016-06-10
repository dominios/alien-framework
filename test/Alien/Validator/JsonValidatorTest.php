<?php

class JsonValidatorTest extends PHPUnit_Framework_TestCase
{

    protected $ruleSet;

    public function setUp ()
    {
        $this->ruleSet = [
            'name' => new Alien\Constraint\Length(2, 7),
            'email' => new Alien\Constraint\Email(),
            'foo' => new Alien\Constraint\Required(),
            'age' => [
                new Alien\Constraint\Type(Alien\Constraint\Type::TYPE_INTEGER),
                new Alien\Constraint\Regex('\d+'),
                new Alien\Constraint\Range(0, 99)
            ]
        ];
    }

    public function testJsonValidatorSuccess ()
    {
        $json = [
            'name' => 'Lorem',
            'email' => 'lorem@ipsum.com',
            'foo' => 'bar',
            'age' => 25
        ];

        $validator = new \Alien\Validator\JsonValidator($this->ruleSet);
        $this->assertEquals(true, $validator->validate($json));
    }

    /**
     * @dataProvider failingJsonsProvider
     * @expectedException \Alien\Constraint\Exception\ValidationException
     */
    public function testJsonValidatorFailure ($json)
    {
        $validator = new \Alien\Validator\JsonValidator($this->ruleSet);
        $this->assertEquals(true, $validator->validate($json));
    }

    public function failingJsonsProvider ()
    {
        return [
            [
                [
                    'name' => 'x',
                    'email' => 'lorem@ipsum.com',
                    'foo' => 'bar',
                    'age' => 25
                ]
            ],
            [
                [
                    'name' => 'LoremIpsum',
                    'email' => 'lorem@ipsum.com',
                    'foo' => 'bar',
                    'age' => 25
                ]
            ],
            [
                [
                    'name' => 'Lorem',
                    'email' => 'lorem',
                    'foo' => 'bar',
                    'age' => 25
                ]
            ],
            [
                [
                    'name' => 'Lorem',
                    'email' => 'lorem@ipsum.com',
                    'foo' => '',
                    'age' => 25
                ]
            ],
            [
                [
                    'name' => 'Lorem',
                    'email' => 'lorem@ipsum.com',
                    'foo' => 'bar',
                    'age' => 25.5
                ]
            ],
            [
                [
                    'name' => 'Lorem',
                    'email' => 'lorem@ipsum.com',
                    'foo' => 'bar'
                ]
            ],
            [
                [
                    'name' => 'Lorem',
                    'email' => 'lorem@ipsum.com',
                    'foo' => 'bar',
                    'age' => 259
                ]
            ]
        ];
    }
}