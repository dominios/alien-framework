<?php

namespace Alien\Models\Content;

use Alien\ActiveRecord;
use Alien\Application;
use Alien\Controllers\BaseController;
use Alien\DBConfig;
use Alien\Models\Content\Template;
use \PDO;

class Page implements ActiveRecord, FileInterface {

    const ICON = 'page';
    const BROWSEABLE = true;

    private $id;
    private $name;
    private $template;
    private $seolink;
    private $keywords;
    private $description;
    private $folder;

    public function __construct($identifier, $row = null) {

        if ($row === null) {
            $DBH = Application::getDatabaseHandler();
            if (is_numeric($identifier)) {
                $Q = $DBH->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::PAGES) . ' WHERE id=:i');
                $Q->bindValue(':i', $identifier, PDO::PARAM_INT);
            } else {
                $Q = $DBH->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::PAGES) . ' WHERE seolink=:i');
                $Q->bindValue(':i', $identifier, PDO::PARAM_STR);
            }
            $Q->execute();
            if ($Q->rowCount()) {
                $row = $Q->fetch();
            }
        }

        if ($row === null) {
            return;
        }

        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->template = $row['template'];
        $this->seolink = $row['seolink'];
        $this->keywords = explode(' ', $row['keywords']);
        $this->description = $row['description'];
        $this->folder = $row['folder'];
    }

    public static function getList($fetch = false) {
        $DBH = Application::getDatabaseHandler();
        $arr = array();
        $STH = $DBH->prepare("SELECT * FROM " . DBConfig::table(DBConfig::PAGES));
        $STH->execute();
        while ($item = $STH->fetch()) {
            $arr[] = $fetch ? new Page($item['id'], $item) : $item['id'];
        }
        return $arr;
    }

    /**
     * @param array $initialValues
     * @return Page page
     */
    public static function create($initialValues) {
        $DBH = Application::getDatabaseHandler();
        $Q = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::PAGES) . '
             (folder, name, seolink, template)
             VALUES (:folder, :name, :seo, :template);');
        $Q->bindValue(':folder', $initialValues['folderId'], PDO::PARAM_INT);
        $Q->bindValue(':name', $initialValues['pageName'], PDO::PARAM_STR);
        $Q->bindValue(':seo', $initialValues['pageSeolink'], PDO::PARAM_STR);
        $Q->bindValue(':template', $initialValues['pageTemplate'], PDO::PARAM_INT);
        return $Q->execute() ? new Page($DBH->lastInsertId()) : false;
    }

    public function isBrowseable() {
        return self::BROWSEABLE;
    }

    public function delete() {
        $DBH = Application::getDatabaseHandler();
        $STH = $DBH->prepare('DELETE FROM ' . DBConfig::table(DBConfig::PAGES) . ' WHERE id=:i LIMIT 1');
        $STH->bindValue(':i', $this->getId(), PDO::PARAM_INT);
        $STH->execute();
        return (bool) $STH->rowCount();
    }

    public function update() {
        $DBH = Application::getDatabaseHandler();
        $Q = $DBH->prepare('UPDATE ' . DBConfig::table(DBConfig::PAGES) . ' SET template=:t, folder=:f, name=:n, description=:desc, seolink=:seo, keywords=:kw WHERE id=:i LIMIT 1;');
        $Q->bindValue(':i', $this->getId(), PDO::PARAM_INT);
        $Q->bindValue(':n', $this->getName(), PDO::PARAM_STR);
        $Q->bindValue(':desc', $this->getDescription(), PDO::PARAM_STR);
        $Q->bindValue(':t', $this->getTemplate(), PDO::PARAM_INT);
        $Q->bindValue(':f', $this->getFolder(), PDO::PARAM_INT);
        $Q->bindValue(':seo', $this->getSeolink(), PDO::PARAM_STR);
        $Q->bindValue(':kw', implode(' ', $this->getKeywords()), PDO::PARAM_STR);
        return $Q->execute();
    }

    public static function exists($identifier) {
        $DBH = Application::getDatabaseHandler();
        if (is_numeric($identifier)) {
            $Q = $DBH->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::PAGES) . ' WHERE id=:i');
            $Q->bindValue(':i', $identifier, PDO::PARAM_INT);
        } else {
            $Q = $DBH->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::PAGES) . ' WHERE seolink=:i');
            $Q->bindValue(':i', $identifier, PDO::PARAM_STR);
        }
        $Q->execute();
        return $Q->rowCount() ? true : false;
    }

    public function actionEdit() {
        return BaseController::actionUrl('page', 'edit', array('id' => $this->getId()));
    }

    public function actionGoTo() {
        return $this->actionEdit();
    }

    public function actionDrop() {
        return BaseController::actionURL('page', 'drop', array('id' => $this->getId()));
    }

    public function getIcon() {
        return self::ICON;
    }

    public static function isSeolinkInUse($seolink, $ignoreId = null) {
        $DBH = Application::getDatabaseHandler();
        $Q = $DBH->prepare('SELECT id FROM ' . DBConfig::table(DBConfig::PAGES) . ' WHERE seolink=:s LIMIT 1;');
        $Q->bindValue(':s', $seolink, PDO::PARAM_STR);
        $Q->execute();
        if (!$Q->rowCount()) {
            return false;
        }
        if ($ignoreId === null && $Q->rowCount()) {
            return true;
        }
        if ($ignoreId !== null) {
            $R = $Q->fetch();
            return $R['id'] == $ignoreId ? false : true;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getSeolink() {
        return $this->seolink;
    }

    /**
     *
     * @param bool $fetch
     * @return Template
     */
    public function getTemplate($fetch = false) {
        if ($fetch) {
            if ($this->template instanceof Template) {
                return $this->template;
            } else {
                $this->template = new Template($this->template);
                return $this->template;
            }
        } else {
            if ($this->template instanceof Template) {
                return $this->template->getId();
            } else {
                return $this->template;
            }
        }
    }

    public function getTitle() {
        return $this->getName();
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function getDescription() {
        return $this->description;
    }

    public function isVisible() {
        return (bool) $this->visible;
    }

    public function getFolder() {
        return $this->folder;
    }

    public function isUsed() {
        return false;
    }

    public function isDeletable() {
        return true;
    }

    public function setTemplate(Template $template) {
        $this->template = $template;
        return $this;
    }

    public function setSeolink($seolink) {
        $this->seolink = $seolink;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

}
