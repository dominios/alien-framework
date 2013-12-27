<?php

namespace Alien\Models\Content;

use Alien\ActiveRecord;
use Alien\Alien;
use Alien\Layout\Layout;
use Alien\Controllers\BaseController;
use Alien\DBConfig;
use \PDO;

class Template extends Layout implements ActiveRecord, FileInterface {

    const ICON = 'template';
    const BROWSEABLE = true;

    private $id;
    private $folder;
    private $name;
    private $description;
    private $src;
    private $blocks;

    public function __construct($id = null, $row = null) {
        $new = false;
        if ($row === null) {
            $DBH = Alien::getDatabaseHandler();
            $STH = $DBH->prepare("SELECT * FROM " . DBConfig::table(DBConfig::TEMPLATES) . " WHERE id_t=:id LIMIT 1");
            $STH->bindValue(':id', $id);
            $STH->execute();
            if (!$STH->rowCount()) {
                $new = true;
            }
            $row = $STH->fetch();
        }
        if ($new) {
            $this->id = null;
            $this->name = '';
            $this->description = '';
            $this->src = '';
            $this->blocks = array();
            $this->folder = new Folder($row['id_f']);
            return;
        }
        $this->id = $row['id_t'];
        $this->name = $row['name'];
        $this->src = $row['src'];
        $this->description = $row['description'];
//        $this->blocks=parse_ini_file($row['config_url']);
//        $this->fetchBlocks();
//        $this->fetchViews();
    }

    protected function getSRC() {
        return $this->src;
    }

    public static function fetchAll($fetch = false) {
        $DBH = Alien::getDatabaseHandler();
        $arr = array();
        $STH = $DBH->prepare("SELECT * FROM " . DBConfig::table(DBConfig::TEMPLATES));
        $STH->execute();
        while ($item = $STH->fetch()) {
            $arr[] = $fetch ? new Template($item['id_t'], $item) : $item['id_t'];
        }
        return $arr;
    }

    public static function exists($id) {
        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->query('SELECT 1 FROM ' . DBConfig::table(DBConfig::TEMPLATES) . '
            WHERE id_t="' . (int) $id . '"')->execute();
        if ($Q->rowCount()) {
            return true;
        } else {
            return false;
        }
    }

    public function save() {
        $DBH = Alien::getDatabaseHandler();
        $new = $this->id === null ? true : false;
        if ($new) {
            $Q = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::TEMPLATES) . '
             (id_f, name, src, description)
             VALUES (:idf, :name, :src, :desc);');
        } else {
            $Q = $DBH->prepare('UPDATE ' . DBConfig::table(DBConfig::TEMPLATES) . '
            SET id_f=:idf, name=:name, src=:src, description=:desc
            WHERE id_t=:i'
            );
            $Q->bindValue(':i', $this->id, PDO::PARAM_INT);
        }
        $Q->bindValue(':idf', $this->folder->getId(), PDO::PARAM_INT);
        $Q->bindValue(':name', $this->name, PDO::PARAM_STR);
        $Q->bindValue(':src', $this->src, PDO::PARAM_STR);
        $Q->bindValue(':desc', $this->description, PDO::PARAM_STR);
        $ret = $Q->execute();
        if ($new && $ret) {
            $this->id = $DBH->lastInsertId();
        }
        return $ret;
    }

    public function delete() {
        if ($this->id === null) {
            return false;
        }
        if ($this->isUsed()) {
            return false;
        }
        $DBH = Alien::getDatabaseHandler();
        return $DBH->query('DELETE FROM ' . DBConfig::table(DBConfig::TEMPLATES) . '
            WHERE id_t=' . $this->id . ' LIMIT 1;')->execute();
    }

    public function isBrowseable() {
        return self::BROWSEABLE;
    }

    public function actionEdit() {
        return BaseController::actionUrl('content', 'editTemplate', array('id' => $this->id));
    }

    public function actionGoTo() {
        return $this->actionEdit();
    }

    public function actionDrop() {
        return BaseController::actionUrl('content', 'dropTemplate', array('id' => $this->id));
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getSrcURL() {
        return $this->src;
    }

    public function getDescription() {
        return $this->description;
    }

    public function isUsed() {
        global $DBH;
        $STH = $DBH->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::PAGES) . ' WHERE id_t=:id');
        $STH->bindValue(':id', $this->id, PDO::PARAM_INT);
        $STH->execute();
        if ($STH->rowCount()) {
            return true;
        } else {
            return false;
        }
    }

//    public function renderControlPanel() {
//        echo('<div style="float: right; display: inline-block; position: relative;">');
//        $editAction = '?page=content&amp;action=editTemplate&amp;id=' . $this->id;
//        $deleteAction = 'javascript: if(confirm(\'Naozaj odstrániť túto šablónu?\')) window.location=\'?page=content&amp;action=dropTemplate&amp;id=' . $this->id . '\'';
//        if (Authorization::permissionTest(null, Array('TEMPLATE_EDIT', 'CONTENT_EDIT')))
//            echo ('<a href="' . $editAction . '"><img class="button" src="images/icons/layout_edit.png" title="Edit template" alt="Edit"></a>');
//        if (Authorization::permissionTest(null, Array('TEMPLATE_EDIT', 'CONTENT_EDIT')))
//            echo ($this->isUsed() ? '' : '<a href="#" onClick="' . $deleteAction . '"><img class="button" src="images/icons/layout_delete.png" title="Delete template" alt="delete"></a>');
//        echo ('</div><br style="clear: right;">');
//    }
//    public function getFolderRenderOptions() {
//        $options = Array();
//        $options['image'] = 'images/icons/template.png';
//        $options['name'] = $this->name;
//        return $options;
//    }
//    public function renderInFolder() {
//        $options = $this->getFolderRenderOptions();
//        echo ('<div class="item"><img src="' . $options['image'] . '"> <b>' . $options['name'] . '</b>');
//        $this->renderControlPanel();
//        echo ('&nbsp;&nbsp;ID: ' . $this->id . '&nbsp;|&nbsp;' . $this->description . ' &nbsp;|&nbsp;' . obsahZdroj . ': ' . $this->html_url);
//        echo ('</div>');
//    }
//    public function sortItems($items) {
//        $DBH = Alien::getDatabaseHandler();
//        $STH = $DBH->prepare("UPDATE " . Alien::getDBPrefix() . "_content_views SET position=:p WHERE id_v=:id");
//        foreach ($items as $string) {
//            $i = 1;
//            $data = explode(',', $string);
//            foreach ($data as $item) {
//                $STH->bindValue(':id', $item, PDO::PARAM_INT);
//                $STH->bindValue(':p', $i++, PDO::PARAM_INT);
//                $STH->execute();
//            }
//        }
//        return;
//    }

    public function getIcon() {
        return self::ICON;
    }

    public static function isTemplateNameInUse($name, $ignoreId = null) {
        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->prepare('SELECT id_t FROM ' . DBConfig::table(DBConfig::TEMPLATES) . ' WHERE name=:n');
        $Q->bindValue(':n', $name, PDO::PARAM_STR);
        $Q->execute();
        if ($ignoreId === null) {
            return $Q->rowCount() ? true : false;
        } else {
            if (!$Q->rowCount()) {
                return false;
            }
            $R = $Q->fetch();
            return $R['id_t'] == $ignoreId ? false : true;
        }
    }

//    private function fetchBlocks() {
//        $blocks = Array();
//        $ini = parse_ini_file($this->getConfigUrl());
//        $i = 1;
//        foreach ($ini as $k => $v) {
//            $id = (int) substr($k, 3);
//            $bl = Array('id' => $id, 'name' => $v, 'items' => Array());
//            $blocks[] = $bl;
//            $i++;
//        }
//        $this->blocks = $blocks;
//    }
//    public function fetchViews() {
//        $DBH = Alien::getDatabaseHandler();
//
//        $blocks = $this->blocks;
//        $newBlocks = Array();
//
//        foreach ($blocks as $block) {
//            $items = Array();
//            foreach ($DBH->query('SELECT * FROM ' . Alien::getDBPrefix() . '_content_views WHERE id_c = ' . (int) $block['id'] . ' && id_t=' . $this->getId() . ' ORDER BY position') as $R) {
//                $item = Widget::getSpecificWidget($R['id_v'], $R['id_type'], $R);
//                if ($item !== null) {
//                    $item->fetchItem();
//                    $items[] = $item;
//                }
//            }
//            $newBlocks[] = Array('id' => $block['id'], 'name' => $block['name'], 'items' => $items);
//        }
//
//        $this->blocks = $newBlocks;
//    }

    public function getPartials() {
        $meta = array(
            'title' => '',
            'description' => '',
            'keywords' => array()
        );
        $blocks = array();
        $partials = array_merge($meta, $blocks);
        return $partials;
    }

    public function handleResponse(\Alien\Response $response) {

    }

    public function isDeletable() {
        
    }

    public function update() {

    }

    public static function create($initialValues) {

    }

    public static function getList($fetch = false) {

    }

}
