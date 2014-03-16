<?php

namespace Alien\Models\Content;

use PDO;
use Alien\Application;
use Alien\ActiveRecord;
use Alien\DBConfig;

class TemplateBlock implements FileInterface, ActiveRecord {

    const ICON = 'puzzle';

    private $id;
    private $label;
    private $template = null;

    public function __construct($id, $row = null) {
        if ($row === null) {
            $DBH = Application::getDatabaseHandler();
            $Q = $DBH->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::BLOCKS)
                . ' WHERE id=:i'
                . ' LIMIT 1;');
            $Q->bindValue(':i', $id, PDO::PARAM_INT);
            $Q->execute();
            $row = $Q->fetch();
        }
        $this->id = $row['id'];
        $this->label = $row['label'];
    }

    public function setTemplate(Template $template) {
        $this->template = $template;
        return $this;
    }

    public function getWidgets(Template $template = null) {
        if ($template == null) {
            $template = $this->template;
        }
        $ret = array();
        if ($template instanceof Template) {
            $DBH = Application::getDatabaseHandler();
            $query = 'SELECT * FROM ' . DBConfig::table(DBConfig::WIDGETS)
                . ' WHERE container = "' . (int) $this->id . '";';
            foreach ($DBH->query($query) as $row) {
                $ret[] = Widget::factory($row['id'], $row['type'], $row);
            }
        } else {
            throw new \UnexpectedValueException("Template cannot be null!");
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
        $DBH = Application::getDatabaseHandler();
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
        return $this->id;
    }

    public function getName() {
        return $this->label;
    }

    public function isBrowseable() {

    }

}
