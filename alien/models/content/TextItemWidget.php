<?php

namespace Alien\Models\Content;

use Alien\Application;
use Alien\DBConfig;
use Alien\Forms\Form;
use Alien\Forms\Input;
use \PDO;

class TextItemWidget extends Widget {

    const ICON = 'document';
    const NAME = 'Textový objekt';
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
        return self::NAME . ': ' . ($this->getItem(true) === null ? '[prázdny]' : $this->getItem(true)->getName());
    }

    public function getType() {
        return self::TYPE;
    }

    public function getCustomFormElements() {
        if (is_null($this->formElements)) {
            $selected = null;
            $widetModel = Input::select('widetModel')
                               ->setLabel('Objekt')
                               ->setIcon('icon-document');
            $widetModel->addOption(new Input\Option('...', Input\Option::TYPE_SELECT, '\N'));
            foreach (TextItem::getList(true) as $item) {
                $opt = new Input\Option($item->getName(), Input\Option::TYPE_SELECT, $item->getId());
                if ($this->getItem() == $item->getId()) {
                    $selected = $opt;
                }
                $widetModel->addOption($opt);
            }
            if ($selected !== null) {
                $widetModel->selectOption($selected);
            }

            $this->formElements = array(
                $widetModel
            );
        }
        return $this->formElements;
    }

    public function handleCustomFormElements(Form $form) {
        $idemId = $form->getElement('widetModel')->getValue();
        $this->setItem(Item::factory($idemId));
    }
}
