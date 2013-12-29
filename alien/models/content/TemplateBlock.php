<?php

namespace Alien\Models\Content;

use PDO;
use Alien\Alien;
use Alien\ActiveRecord;
use Alien\DBConfig;

class TemplateBlock implements FileInterface, ActiveRecord {

    const ICON = 'puzzle';

    private $id;
    private $label;
    private $template = null;

    public function __construct($id, $row = null) {
        if ($row === null) {
            $DBH = Alien::getDatabaseHandler();
            $Q = $DBH->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::BLOCKS)
                    . ' WHERE id_b=:i'
                    . ' LIMIT 1;');
            $Q->bindValue(':i', $id, PDO::PARAM_INT);
            $Q->execute();
            $row = $Q->fetch();
        }
        $this->id = $row['id_b'];
        $this->label = $row['label'];
    }

    public function setTemplate(Template $template) {
        $this->template = $template;
        return $this;
    }

    public function getWidgets() {
        $ret = array();
        if ($this->template instanceof Template) {
            $DBH = Alien::getDatabaseHandler();
            $query = 'SELECT * FROM ' . DBConfig::table(DBConfig::WIDGETS)
                    . ' WHERE container = "' . (int) $this->id . '";';
            foreach ($DBH->query($query) as $row) {
                $ret[] = Widget::getSpecificWidget($row['id_v'], $row['id_type'], $row);
//                $ret[] = $row;
            }
        }
        return $ret;
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
