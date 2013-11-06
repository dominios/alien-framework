<?php

namespace Alien\Models\Content;

use Alien\Alien;
use \PDO;

class CodeItemWidget extends Widget {

    const ICON = 'code.png';
    const NAME = 'HTML kód';
    const TYPE = 'CodeItem';

    public function __construct($id, $row = null) {
        parent::__construct($id, $row);
    }

    public function getIcon() {
        return self::ICON;
    }

    public function getName() {
        return self::NAME . ': ' . $this->getItem(true)->renderToString();
    }

    public function getType() {
        return self::TYPE;
    }

//    public function setItem(CodeItem $item) {
//        $this->item = $item;
//    }
}
