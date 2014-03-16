<?php

namespace Alien\Models\Content;

use Alien\Application;
use Alien\Controllers\BaseController;
use Alien\Controllers\TextItemController;
use Alien\DBConfig;
use \PDO;

class TextItem extends Item {

    const Icon = 'document';

    public function __construct($id, $row = null) {
        parent::__construct($id, $row);
    }

    public static function getList($fetch = false) {
        $ret = array();
        $dbh = Application::getDatabaseHandler();
        foreach ($dbh->query('SELECT * FROM ' . DBConfig::table(DBConfig::ITEMS) . ' WHERE type="' . stripNamespace(__CLASS__) . '";') as $row) {
            $ret[] = $fetch ? new TextItem($row['id'], $row) : $row['id'];
        }
        return $ret;
    }

    public function isBrowseable() {
        return true;
    }

    public function actionGoTo() {
        // TODO: Implement actionGoTo() method.
    }

    public function actionEdit() {
        return BaseController::actionURL('textItem', 'edit', array('id' => $this->getId()));
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


    public function update() {
        $dbh = Application::getDatabaseHandler();
        $q = $dbh->prepare('UPDATE ' . DBConfig::table(DBConfig::ITEMS) . ' SET name=:nm, content=:cont WHERE id=:id LIMIT 1;');
        $q->bindValue(':id', $this->getId());
        $q->bindValue('nm', $this->getName());
        $q->bindValue(':cont', $this->getContent());
        return $q->execute();
    }

    public function isDeletable() {
        return true;
    }

    public static function create($initialValues) {
        // TODO: Implement create() method.
    }
}