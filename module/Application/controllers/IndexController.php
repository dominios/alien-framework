<?php

namespace Application\Controllers;

use Alien\Mvc\AbstractController;
use Alien\Mvc\Component\NavigationComponent;
use Alien\Mvc\Component\TextComponent;
use Alien\Mvc\View;

class IndexController extends AbstractController
{

    protected function prepareView($action)
    {
        return new View(__DIR__ . '/../views/index/' . str_replace('Action', '', $action) . '.phtml');
    }

    protected function createComponentFromFactory($name) {
        $config = $this->getServiceLocator()->getService('Alien\Configuration');
        $factories = $config->get('controllers')[__CLASS__]['components'];
        return $factories[$name]();
    }

    protected function indexAction()
    {

        $this->view->setVariable('projectName', 'ALiEN Framework CSM');
        $this->view->addComponent($this->createComponentFromFactory('nav'));
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