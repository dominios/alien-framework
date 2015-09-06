<?php

namespace Application;

use Alien\Mvc\AbstractController;
use Alien\Mvc\Component\TextComponent;
use Alien\Mvc\View;

class IndexController extends AbstractController {

    protected function prepareView($action)
    {
        return new View(__DIR__ . '/views/' . str_replace('Action', '', $action) . '.phtml');
    }

    protected function indexAction() {

        $this->view->addComponent($this->createComponentParagraph());

        $this->getResponse()->setContentType('text/html;charset=UTF8');
        $this->getResponse()->setContent($this->view->render());
    }

    public function createComponentParagraph() {
        $textComponent = new TextComponent('Paragraph');
        $textComponent->setContent('Lorem ipsum dolor sit amet,
            everti oblique conclusionemque nam an, facilisis definitionem cu eos.
            Omnis lorem salutatus ei qui. Cum wisi nonumy ei, et maiorum recusabo disputando ius.
            Probatus eleifend forensibus cu sea, mucius assueverit eu vim.
        ');
        return $textComponent;
    }
}