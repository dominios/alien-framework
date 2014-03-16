<?php

namespace Alien\Models\Content;

use Alien\Application;
use Alien\DBConfig;
use \PDO;

abstract class Item implements FileInterface {

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

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public abstract function getType();

    public static abstract function getList();

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

}