<?php

namespace Alien\Models\Content;

use PDO;
use Alien\Alien;
use Alien\ActiveRecord;
use Alien\DBConfig;

class TemplateBlock implements FileInterface, ActiveRecord {

    const ICON = 'puzzle';

    private $label;

    public function __construct($id, $row = null) {
        if ($row === null) {

        }
        $this->label = $row['label'];
    }

    public function delete() {

    }

    public function isDeletable() {

    }

    public function update() {

    }

    public static function create($initialValues) {

    }

    public static function exists($id) {

    }

    public static function getList($fetch = false) {
        $DBH = Alien::getDatabaseHandler();
        $arr = array();
        $STH = $DBH->prepare("SELECT * FROM " . DBConfig::table(DBConfig::BLOCKS));
        $STH->execute();
        while ($row = $STH->fetch()) {
            $arr[] = $fetch ? new TemplateBlock($row['id_b'], $row) : $row['id_b'];
        }
        return $arr;
    }

    public function actionDrop() {

    }

    public function actionEdit() {

    }

    public function actionGoTo() {

    }

    public function getIcon() {
        return self::ICON;
    }

    public function getId() {

    }

    public function getName() {
        return $this->label;
    }

    public function isBrowseable() {

    }

}
