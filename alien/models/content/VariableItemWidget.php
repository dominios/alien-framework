<?php

namespace Alien\Models\Content;

use Alien\Application;
use Alien\DBConfig;
use Alien\Forms\Form;
use Alien\Forms\Input;

class VariableItemWidget extends Widget implements HasContainerInterface {

    const ICON = 'variable';
    const NAME = 'Variabilný objekt';
    const TYPE = 'VariableItem';

    private $widgetContainer;
    private $hasFetchedWidgets = false;

    public function renderToString(Item $item = null) {
        $ret = '';
        if(!$this->isContainerContentFetched()) {
            $this->fetchContainerContent();
        }
        foreach ($this->getWidgetContainer() as $widget) {
            if ($widget instanceof Widget) {
                $ret .= $widget->__toString();
            }
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
        $size = $form->getField('variableSize')->getValue();
        $name = $form->getField('variableName')->getValue();
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

    public function fetchContainerContent() {
        if ($this->isContainerContentFetched()) {
            return;
        }
        if (!($this->getPageToRender() instanceof Page)) {
            return;
        }
        $dbh = Application::getDatabaseHandler();
        foreach ($dbh->query('SELECT * FROM ' . DBConfig::table(DBConfig::WIDGETS) . ' WHERE page="' . $this->getPageToRender()->getId() . '" && container="' . $this->getId() . '"') as $row) {
            $this->getWidgetContainer()->push(Widget::factory($row['id'], null, $row));
        }
        $this->hasFetchedWidgets = true;
    }

    public function getWidgetContainer() {
        if (!($this->widgetContainer instanceof WidgetContainer)) {
            $this->widgetContainer = new WidgetContainer();
        }
        return $this->widgetContainer;
    }

    public function flushContainerContent() {
        $this->getWidgetContainer()->truncate();
        $this->hasFetchedWidgets = false;
    }

    public function isContainerContentFetched() {
        return $this->hasFetchedWidgets;
    }
}

