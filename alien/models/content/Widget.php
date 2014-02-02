<?php

namespace Alien\Models\Content;

use Alien\ActiveRecord;
use Alien\Alien;
use Alien\DBConfig;
use Alien\Controllers\BaseController;
use Alien\View;
use \PDO;

abstract class Widget implements FileInterface, ActiveRecord {

    const ICON = 'file.png';
    const NAME = 'Zobrazovač';
    const TYPE = 'ItemView';
    const WIDGET_FOLDER = 'widgets';
    const DEFAULT_SCRIPT = '';

    protected $id;
    protected $container;
    protected $type;
    protected $item = null;
    protected $position;
    protected $visible;
    protected $params;
    protected $page;
    protected $template;
    protected $class = '';
    private $script = '';
    private $view = null;

    public function __construct($id, $row = null) {

        if ($row === null) {
            $DBH = Alien::getDatabaseHandler();
            $Q = $DBH->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::WIDGETS) . ' WHERE id = :i');
            $Q->bindValue(':i', $id, PDO::PARAM_INT);
            $Q->execute();
            if (!$Q->rowCount()) {
                return;
            }
            $row = $Q->fetch();
        }

        $this->id = $row['id'];
        $this->container = $row['container'];
        $this->type = $row['type'];
        $this->item = $row['item'];
        $this->item = $this->fetchItem();
        $this->position = (int) $row['position'];
        $this->visible = (bool) $row['visible'];
        $this->params = unserialize($row['params']);
        $this->page = $row['page'];
        $this->template = $row['template'];
        $this->class = $row['class'];
        $this->script = $row['script'];
    }

    public static final function getSpecificWidget($idView, $idType = null, $row = null) {

        if ($row === null) {

            if ($idType !== null) {
                $cond = 'type = ' . $idType;
            } else {
                $cond = 'id = ' . (int) $idView;
            }

            $DBH = Alien::getDatabaseHandler();
            $row = $DBH->query('SELECT * FROM ' . DBConfig::table(DBConfig::WIDGETS)
                . ' WHERE ' . $cond
                . ' LIMIT 1;')->fetch();
        }

        if (sizeof($row) && $row !== null) {
            $classname = __NAMESPACE__ . '\\' . $row['type'];
            if (class_exists($classname)) {
                $widget = new $classname($idView, $row);
                return $widget;
            }
        }
        return null;
    }

    public abstract function renderToString(ContentItem $item = null);

    public final function getView() {
        if (!($this->view instanceof View)) {
            $file = './' . Widget::WIDGET_FOLDER . '/' . $this->getScript();
            $view = new \Alien\View($file);
            $this->view = $view;
        }
        return $this->view;
    }

    public function __toString() {
        return $this->renderToString();
    }

    public abstract function getIcon();

    public abstract function getName();

    public abstract function getType();

    public function getId() {
        return $this->id;
    }

    public function getContainer() {
        return $this->container;
    }

    public function getParams() {
        return $this->params;
    }

    public function getParam($key) {
        return $this->params[$key];
    }

    public function getPosition() {
        return $this->position;
    }

    public function getItem($fetch = false) {
        if ($this->item === null) {
            return null;
        }
        if ($fetch) {
            if ($this->item instanceof ContentItem) {
                return $this->item;
            } else {
                $this->item = ContentItem::getSpecificItem($this->item);
                return $this->item;
            }
        } else {
            if ($this->item instanceof ContentItem) {
                return $this->item->getId();
            } else {
                return $this->item;
            }
        }
    }

    public function fetchItem() {
        if (!($this->item instanceof ContentItem)) {
            $this->item = ContentItem::getSpecificItem($this->item);
        }
        return $this->item;
    }

    public function setItem(ContentItem $item) {
        $this->item = $item;
    }

    public function getPage($fetch = false) {
        if ($fetch) {
            if ($this->page instanceof Page) {
                return $this->page;
            } else {
                $this->page = new Page($this->page);
                return $this->page;
            }
        } else {
            if ($this->page instanceof Page) {
                return $this->page->getId();
            } else {
                return $this->page;
            }
        }
    }

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

    public function getClass() {
        return $this->class;
    }

    public function getScript() {
        if (!strlen($this->script)) {
            $class = get_class($this);
            return $class::DEFAULT_SCRIPT;
        } else {
            return $this->script;
        }
    }

    public function isVisible() {
        return (bool) $this->visible;
    }

    public function actionEdit() {
        return BaseController::actionURL('content', 'editWidget', array('id' => $this->id));
    }

    public function actionDrop() {
        return BaseController::actionURL('content', 'dropView', array('id' => $this->id));
    }

    public function update() {
        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->prepare('UPDATE ' . DBConfig::table(DBConfig::WIDGETS)
            . ' SET visible=:v, position=:pos, class=:c, script=:s, params=:parm, page=:pg, template=:tmpl WHERE id=:id LIMIT 1;');

        $page = $this->getPage(true);
        $pg = $page instanceof Page ? $page->getId() : null;
        $template = $this->getTemplate(true);
        $tmpl = $template instanceof Template ? $template->getId() : null;

        $Q->bindValue(':id', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':pg', $pg);
        $Q->bindValue(':tmpl', $tmpl);
        $Q->bindValue(':c', $this->class, PDO::PARAM_STR);
        $Q->bindValue(':s', $this->script, PDO::PARAM_STR);
        $Q->bindValue(':v', $this->visible, PDO::PARAM_BOOL);
        $Q->bindValue(':pos', $this->position, PDO::PARAM_STR);
        $Q->bindValue(':parm', serialize($this->params), PDO::PARAM_STR);
        return (bool) $Q->execute();
    }

    public function delete() {
        // TODO: Implement delete() method.
    }

    public function isDeletable() {
        // TODO: Implement isDeletable() method.
    }

    public static function create($initialValues) {
        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::WIDGETS) . '
            (type, visible, position, container) VALUES (:t, :v, :p, :c);');
        $Q->bindValue(':t', $initialValues['type'], PDO::PARAM_STR);
        $Q->bindValue(':v', $initialValues['visible'], PDO::PARAM_BOOL);
        $Q->bindValue(':p', $initialValues['position'], PDO::PARAM_STR);
        $Q->bindValue(':c', $initialValues['container'], PDO::PARAM_INT);
        return $Q->execute() ? Widget::getSpecificWidget($DBH->lastInsertId()) : null;
    }

    public static function exists($id) {
        // TODO: Implement exists() method.
    }

    public static function getList($fetch = false) {
        // TODO: Implement getList() method.
    }

    public function isBrowseable() {
        // TODO: Implement isBrowseable() method.
    }

    public function actionGoTo() {
        // TODO: Implement actionGoTo() method.
    }

    public function setPage(Page $page) {
        $this->page = $page;
    }

    public function setTemplate(Template $template) {
        $this->template = $template;
    }

    public function setPosition($position) {
        $this->position = $position;
    }

}
