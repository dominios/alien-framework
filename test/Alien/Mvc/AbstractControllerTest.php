<?php

use Alien\Mvc\AbstractController;

class TestController extends AbstractController
{

    public function emptyAction()
    {
        return new \Alien\Mvc\Response();
    }

    public function argumentAction($id)
    {
        $this->getResponse()->id = $id;
    }
}

class AbstractControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TestController
     */
    private $ctrl;

    public function setUp()
    {
        $this->ctrl = new TestController();
    }

    public function testActionCall()
    {
        $ctrl = $this->ctrl;
        $ctrl->clearQueue();

        $route = new \Alien\Routing\Route('TestController', 'argument', '/test/argument', ['id' => 123456]);

        $ctrl->addAction('empty');
        $ctrl->addAction($route);

        $response = $ctrl->getResponses();

        $this->assertInstanceOf('\Alien\Mvc\Response', $response[0]);
        $this->assertInstanceOf('\Alien\Mvc\Response', $response[1]);
        $this->assertEquals(123456, $response[1]->id);


    }

}