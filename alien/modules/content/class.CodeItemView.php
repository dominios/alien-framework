<?php

class CodeItemView extends ContentItemView {

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

}

