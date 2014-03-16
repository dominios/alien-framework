<?php

namespace Alien\Models\Content;

use Alien\Application;
use Alien\DBConfig;
use Alien\Forms\Form;
use Alien\Forms\Input;
use \PDO;

class TextItemWidget extends Widget {

    const ICON = 'document';
    const NAME = 'Text';
    const TYPE = 'TextItem';
    const DEFAULT_SCRIPT = 'CodeItem.php';

    public function __construct($id, $row = null) {
        parent::__construct($id, $row);
    }

    public function renderToString(Item $item = null) {
//        $item = $item instanceof ContentItem ? $item : $this->getItem(true);
        $params = $this->getParams();
        $view = $this->getView();
        $view->text = $params['text'];
        return $view->renderToString();
    }

    public function getIcon() {
        return self::ICON;
    }

    public function getName() {
        return self::NAME . ': ' . $this->getParam('text');
    }

    public function getType() {
        return self::TYPE;
    }

    public function getCustomFormElements() {
        if (is_null($this->formElements)) {

            $widetModel = Input::select('widetModel')
                               ->setLabel('Objekt')
                               ->setIcon('icon-document');
            $widetModel->addOption(new Input\Option('...', 'bbb', '\N'));
            foreach (TextItem::getList() as $item) {
                $opt = new Input\Option($item->getName(), $item->getId(), $item->getId());
                $widetModel->addOption($opt);
            }


            $this->formElements = array(
                $widetModel
            );
        }
        return $this->formElements;
    }

    public function handleCustomFormElements(Form $form) {
        $text = $form->getElement('textWidgetText')->getValue();
        $this->setParam('text', $text);
    }
}
