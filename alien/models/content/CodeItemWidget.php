<?php

namespace Alien\Models\Content;

use Alien\Alien;
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

}
