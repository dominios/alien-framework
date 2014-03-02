<?php

namespace Alien\Models\Content;

use Alien\Alien;
use Alien\DBConfig;
use Alien\Forms\Form;
use Alien\Forms\Input;
use \PDO;

class CodeItemWidget extends Widget {

    const ICON = 'code';
    const NAME = 'HTML kód';
    const TYPE = 'CodeItem';
    const DEFAULT_SCRIPT = 'CodeItem.php';

    public function __construct($id, $row = null) {
        parent::__construct($id, $row);
    }

    public function renderToString(ContentItem $item = null) {
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
            $this->formElements = array(
                Input::text('codeWidgetText', '', $this->getParam('text'))
                     ->setLabel('Text')
                     ->setIcon('icon-comments'),
            );
        }
        return $this->formElements;
    }

    public function handleCustomFormElements(Form $form) {

    }
}
