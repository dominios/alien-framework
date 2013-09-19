<?php

namespace Alien\Models\Content;

use Alien\Alien;
use \PDO;

class VariableItem extends ContentItem {

    const BROWSEABLE = false;

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
        // TODO: Implement getIcon() method.
    }

    public static function exists($id) {
        // TODO: Implement exists() method.
    }

    public function actionGoTo() {
        // TODO: Implement actionGoTo() method.
    }

    public function actionEdit() {
        return BaseController::actionURL('content', 'editItem', array('id' => $this->id));
    }

    public function actionDrop() {
        // TODO: Implement actionDrop() method.
    }

}

