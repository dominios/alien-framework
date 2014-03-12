<?php

namespace Alien\Models\Content;

use Alien\Application;
use \PDO;

class CodeItem extends ContentItem {

    const BROWSEABLE = true;

    public function __construct($id, $row = null) {
        parent::__construct($id, $row);
    }

    public function isBrowseable() {
        return self::BROWSEABLE;
    }

    public function getType() {
        return __CLASS__;
    }

    public function getIcon() {
        return __CLASS__;
    }

    public static function exists($id) {
        // TODO: Implement exists() method.
    }

    public function actionGoTo() {
        // TODO: Implement actionGoTo() method.
    }

    public function actionEdit() {
        // TODO: Implement actionEdit() method.
    }

    public function actionDrop() {
        // TODO: Implement actionDrop() method.
    }

}

