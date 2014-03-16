<?php

namespace Alien\Models\Content;

use Alien\ActiveRecord;
use Alien\Application;
use Alien\DBConfig;
use \PDO;

abstract class Item implements ActiveRecord, FileInterface {

    const Icon = 'file-unknown';

    protected $id;
    protected $name;
    protected $folder;
    protected $type;
    protected $content;
    protected $container;

    public function __construct($id, $row = null) {

        if ($row === null) {
            $DBH = Application::getDatabaseHandler();
            $Q = $DBH->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::ITEMS) . ' WHERE id=:i LIMIT 1;');
            $Q->bindValue(':i', $id, PDO::PARAM_INT);
            $Q->execute();
            if (!$Q->rowCount()) {
                return;
            }
            $row = $Q->fetch();
        }

        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->folder = $row['folder'];
        $this->type = $row['type'];
        $this->container = $row['container'];
        $this->content = $row['content'];
    }

    public static function exists($id) {
        $dbh = Application::getDatabaseHandler();
        $q = $dbh->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::ITEMS) . ' WHERE id=:id LIMIT 1;');
        $q->bindValue(':id', $id, PDO::PARAM_INT);
        $q->execute();
        return (bool) $q->rowCount();
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public abstract function getType();

    public static abstract function getList($fetch = false);

    /**
     * @param $itemId
     * @param null|array $row
     * @return Item|null
     */
    public static final function factory($itemId, $row = null) {
        if ($row === null && is_numeric($itemId)) {
            $DBH = Application::getDatabaseHandler();
            $row = $DBH->query('SELECT * FROM ' . DBConfig::table(DBConfig::ITEMS)
                . ' WHERE id = "' . (int) $itemId . '"'
                . ' LIMIT 1;')->fetch();
        }
        if (sizeof($row) && $row !== null) {
            $classname = __NAMESPACE__ . '\\' . $row['type'];
            if (class_exists($classname)) {
                $item = new $classname($itemId, $row);
                return $item;
            }
        }
        return null;
    }

    public abstract function update();

    public function delete() {
        // TODO: Implement delete() method.
    }

    public abstract function isDeletable();

    public abstract static function create($initialValues);

    public abstract function isBrowseable();

    public abstract function actionGoTo();

    public abstract function actionEdit();

    public abstract function actionDrop();

    public function getContainer() {
        return (int) $this->container;
    }

    /**
     * @return string
     * @deprecated
     */
    public function renderToString() {
        return $this->__toString();
    }

    public function __toString() {
//        return $this->content;
        throw new Exception('Cannot convert item to string!');
    }

    public function getFolder() {
        return $this->folder;
    }

    public function getIcon() {
        return self::Icon;
    }

    public function getContent() {
        return $this->content;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function setContainer($container) {
        $this->container = $container;
        return $this;
    }

    public function setFolder($folder) {
        $this->folder = $folder;
        return $this;
    }

}
