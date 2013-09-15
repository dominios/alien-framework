<?php

abstract class ContentItem implements FileItem {

    const Icon = 'file_unknown.png';

    protected $id;
    protected $name;
    protected $folder;
    protected $type;
    protected $content;
    protected $container;

    public function __construct($id, $row = null) {

        if ($row === null) {
            $DBH = Alien::getDatabaseHandler();
            $Q = $DBH->prepare('SELECT * FROM ' . ALien::getDBPrefix() . '_content_items WHERE id_i=:i LIMIT 1;');
            $Q->bindValue(':i', $id, PDO::PARAM_INT);
            $Q->execute();
            if (!$Q->rowCount()) {
                return null;
            }
            $row = $Q->fetch();
        }

        $this->id = $row['id_i'];
        $this->name = $row['name'];
        $this->folder = $row['id_f'];
        $this->type = $row['id_type'];
        $this->container = $row['id_c'];
        $this->content = $row['content'];
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public abstract function getType();

    public static final function getSpecificItem($idItem, $idType = null, $R = null) {

        if ($idType !== null) {
            $cond = 'id_type = ' . $idType;
        } else {
            $cond = 'id_i = ' . (int) $idItem;
        }
        $DBH = Alien::getDatabaseHandler();
        $row = $DBH->query('SELECT classname FROM ' . Alien::getDBPrefix() . '_content_item_types JOIN ' . Alien::getDBPrefix() . '_content_items USING (id_type) WHERE ' . $cond . ' LIMIT 1')->fetch();
        if (sizeof($row) && $row !== null) {
            $classname = $row['classname'];
            if (class_exists($classname)) {
//                var_dump($idItem, $idType, $R); die;
                return $R === null ? new $classname($idItem) : new $classname(null, $R);
            }
        } else {
            return null;
        }
    }

    public function getContainer() {
        return (int) $this->container;
    }

    public function renderToString() {
        return $this->content;
    }

    public function getFolder() {
        return $this->folder;
    }

    public function getIcon() {
        return self::Icon;
    }

}

