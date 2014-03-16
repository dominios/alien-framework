<?php

namespace Alien\Models\Content;

use Alien\Application;
use Alien\Controllers\TextItemController;
use Alien\DBConfig;
use \PDO;

class TextItem extends Item {

    const Icon = 'document';

    public function __construct($id, $row = null) {
        parent::__construct($id, $row);
    }

    public static function getList() {
        $ret = array();
        $dbh = Application::getDatabaseHandler();
        foreach ($dbh->query('SELECT * FROM ' . DBConfig::table(DBConfig::ITEMS) . ' WHERE type="' . stripNamespace(__CLASS__) . '";') as $row) {
            $ret[] = new TextItem($row['id'], $row);
        }
        return $ret;
    }

    public function isBrowseable() {
        return true;
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

    public function getType() {
        return __CLASS__;
    }

    public function getIcon() {
        return self::Icon;
    }


}