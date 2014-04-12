<?php

namespace Alien\Models\Content;

use Alien\Application;
use Alien\DBConfig;
use Alien\Forms\Form;
use Alien\Forms\Input;
use DomainException;
use PDO;

class VariableItemWidget extends Widget implements HasContainerInterface {

    const ICON = 'variable';
    const NAME = 'Variabilný objekt';
    const TYPE = 'VariableItem';

    private $widgetContainer;

    public function renderToString(Item $item = null) {
        $ret = '';
        foreach ($this->getWidgetContainer() as $widget) {
            $ret .= $widget->__toString();
        }
        return $ret;
    }

    public function getCustomFormElements() {
        if (is_null($this->formElements)) {
            $widgetSize = Input::text('variableSize', 0, $this->getParam('size'))
                               ->setLabel('Max. počet objektov')
                               ->setIcon('icon-hashtag');
            $widgetName = Input::text('variableName', '', $this->getParam('name'))
                               ->setLabel('Názov oblasti')
                               ->setIcon('icon-variable');
            $this->formElements = array(
                $widgetName, $widgetSize
            );
        }
        return $this->formElements;
    }

    public function handleCustomFormElements(Form $form) {
        $size = $form->getElement('variableSize')->getValue();
        $name = $form->getElement('variableName')->getValue();
        $this->setParam('name', $name);
        $this->setParam('size', $size);
    }

    public function getIcon() {
        return self::ICON;
    }

    public function getName() {
        return self::NAME;
    }

    public function getType() {
        return self::TYPE;
    }

    private function fetchWidgets() {
        if (!($this->getPageToRender() instanceof Page)) {
            return;
        }
        $dbh = Application::getDatabaseHandler();
        foreach ($dbh->query('SELECT * FROM ' . DBConfig::table(DBConfig::WIDGETS) . ' WHERE page="' . $this->getPageToRender()->getId() . '" && container="' . $this->getId() . '"') as $row) {
            $this->widgetContainer->push(Widget::factory($row['id'], null, $row));
        }
    }

    public function getWidgetContainer() {
        $this->fetchWidgets();
        if (!($this->widgetContainer instanceof WidgetContainer)) {
            $this->widgetContainer = new WidgetContainer();
        }
        return $this->widgetContainer;
    }

}

