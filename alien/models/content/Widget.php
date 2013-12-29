<?php

namespace Alien\Models\Content;

use Alien\Alien;
use Alien\DBConfig;
use Alien\Controllers\BaseController;
use \PDO;

abstract class Widget {

    const Icon = 'file.png';
    const Name = 'Zobrazovač';
    const Type = 'ItemView';
    const WIDGET_FOLDER = 'widgets';

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
    protected $script = '';

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

    public final function renderToString() {
        $item = $this->getItem(true);
        $file = './' . Widget::WIDGET_FOLDER . '/' . $this->script;

        $view = new \Alien\View($file);
        return $view->renderToString();
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

    public function getPage($fetch = false) {
        if ($fetch) {
            if ($this->item instanceof ContentPage) {
                return $this->page;
            } else {
                $this->item = ContentTemplate($this->page);
                return $this->page;
            }
        } else {
            if ($this->item instanceof ContentPage) {
                return $this->page->getId();
            } else {
                return $this->page;
            }
        }
    }

    public function getTemplate($fetch = false) {
        if ($fetch) {
            if ($this->item instanceof Template) {
                return $this->template;
            } else {
                $this->item = ContentTemplate($this->template);
                return $this->template;
            }
        } else {
            if ($this->item instanceof Template) {
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
        return $this->script;
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

}
