<?php

namespace Alien\Models\Content;

use Alien\Application;
use Alien\DBConfig;
use Alien\Forms\Form;
use Alien\Forms\Input;
use PDO;

class VariableItemWidget extends Widget {

    const ICON = 'variable';
    const NAME = 'Variabilný objekt';
    const TYPE = 'VariableItem';
    const DEFAULT_SCRIPT = 'default.php';

    public function renderToString(Item $item = null) {
        $item = new VariableItem($this->getPageToRender(), $this->getId());
        $ret = '';
        foreach($item->getWidgetContainer() as $widget) {
            $widget->setPageToRender($this->getPageToRender());
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

}

