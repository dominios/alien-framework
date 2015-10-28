<?php

namespace Application\Controllers;

use Alien\Mvc\AbstractController;
use Alien\Mvc\View;
use Application\Models\Cms\Cms;

class IndexController extends AbstractController
{

    private $cms;

    public function __construct() {
        $this->cms = new Cms;
    }

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
        $this->view->setVariable('projectName', 'ALiEN Framework CMS');
        $this->view->addComponent($this->createComponentFromFactory('nav'));
        $this->view->addComponent(new \Application\Models\Cms\Components\Text\TextComponent('Text'));
        $this->view->setVariable('cms', $this->cms->getHeader()->render());
        $this->getResponse()->setContentType('text/html;charset=UTF8');
        $this->getResponse()->setContent($this->view->render());
    }

}