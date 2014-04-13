<?php

namespace Alien\Models\Content;

use Alien\ActiveRecord;
use Alien\Application;
use Alien\DBConfig;
use Alien\Controllers\BaseController;
use Alien\Forms\Form;
use Alien\View;
use DomainException;
use \PDO;

abstract class Widget implements FileInterface, ActiveRecord {

    const ICON = 'file.png';
    const NAME = 'Zobrazovač';
    const TYPE = 'ItemView';
    const DEFAULT_SCRIPT = 'default.php';

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
    protected $formElements = null;
    private $pageToRender = null;

    protected function __construct($id, $row = null) {

        if ($row === null) {
            $DBH = Application::getDatabaseHandler();
            $Q = $DBH->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::WIDGETS) . ' WHERE id = :i');
            $Q->bindValue(':i', $id, PDO::PARAM_INT);
            $Q->execute();
            if (!$Q->rowCount()) {
                throw new DomainException("Requested Item does not exists!");
            }
            $row = $Q->fetch();
        }

        if (!Widget::exists($row['id'])) {
            throw new DomainException("Requested Widget does not exists!");
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

    /**
     * @param $widgetId
     * @param null|string $type
     * @param null|array $row
     * @return Widget
     */
    public static final function factory($widgetId, $type = null, $row = null) {

        if ($row === null) {

            if ($type !== null) {
                $cond = 'type = ' . (string) $type;
            } else {
                $cond = 'id = ' . (int) $widgetId;
            }

            $DBH = Application::getDatabaseHandler();
            $row = $DBH->query('SELECT * FROM ' . DBConfig::table(DBConfig::WIDGETS)
                . ' WHERE ' . $cond
                . ' LIMIT 1;')->fetch();
        }

        if (sizeof($row) && $row !== null) {
            $classname = __NAMESPACE__ . '\\' . $row['type'];
            if (class_exists($classname)) {
                $widget = new $classname($widgetId, $row);
                if ($widget instanceof HasContainerInterface) {
                    $widget->getWidgetContainer();
                }
                return $widget;
            } else {
                throw new \RuntimeException("Undefined widget class '$classname'");
            }
        } else {
            throw new DomainException("Widget not found.");
        }
    }

    public abstract function renderToString(Item $item = null);

    public abstract function getCustomFormElements();

    public function injectCustomFormElements(Form $form) {
        foreach ($this->getCustomFormElements() as $input) {
            $input->addToForm($form);
        }
        return $form;
    }

    public abstract function handleCustomFormElements(Form $form);

    public final function getView() {
        if (!($this->view instanceof View)) {
            $file = $this->getScript();
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

    public function setParam($key, $value) {
        $this->params[$key] = $value;
        return $this;
    }

    public function getPosition() {
        return $this->position;
    }

    public function getItem($fetch = false) {
        if ($this->item === null) {
            return null;
        }
        if ($fetch) {
            if ($this->item instanceof Item) {
                return $this->item;
            } else {
                $this->item = Item::factory($this->item);
                return $this->item;
            }
        } else {
            if ($this->item instanceof Item) {
                return $this->item->getId();
            } else {
                return $this->item;
            }
        }
    }

    public function fetchItem() {
        if (!($this->item instanceof Item)) {
            $this->item = Item::factory($this->item);
        }
        return $this->item;
    }

    public function setItem(Item $item = null) {
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
        return BaseController::actionURL('content', 'dropWidget', array('id' => $this->id));
    }

    public function update() {
        $DBH = Application::getDatabaseHandler();
        $Q = $DBH->prepare('UPDATE ' . DBConfig::table(DBConfig::WIDGETS)
            . ' SET visible=:v, position=:pos, class=:c, script=:s, params=:parm, page=:pg, template=:tmpl, item=:item WHERE id=:id LIMIT 1;');

        $page = $this->getPage(true);
        $pg = $page instanceof Page ? $page->getId() : null;
        $template = $this->getTemplate(true);
        $tmpl = $template instanceof Template ? $template->getId() : null;

        $Q->bindValue(':id', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':pg', $pg);
        $Q->bindValue(':tmpl', $tmpl);
        $Q->bindValue(':item', $this->getItem());
        $Q->bindValue(':c', $this->class, PDO::PARAM_STR);
        $Q->bindValue(':s', $this->script, PDO::PARAM_STR);
        $Q->bindValue(':v', $this->visible, PDO::PARAM_INT);
        $Q->bindValue(':pos', $this->position, PDO::PARAM_STR);
        $Q->bindValue(':parm', serialize($this->params), PDO::PARAM_STR);
        return (bool) $Q->execute();
    }

    public function delete() {
        $dbh = Application::getDatabaseHandler();
        $q = $dbh->prepare('DELETE FROM ' . DBConfig::table(DBConfig::WIDGETS) . ' WHERE id=:i');
        $q->bindValue(':i', (int) $this->getId(), PDO::PARAM_INT);
        $q->execute();
        return $q->rowCount() ? true : false;
    }

    public function isDeletable() {
        // TODO: Implement isDeletable() method.
    }

    public static function create($initialValues) {
        $DBH = Application::getDatabaseHandler();
        $Q = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::WIDGETS) . '
            (type, visible, position, container) VALUES (:t, :v, :p, :c);');
        $Q->bindValue(':t', $initialValues['type'], PDO::PARAM_STR);
        $Q->bindValue(':v', $initialValues['visible'], PDO::PARAM_BOOL);
        $Q->bindValue(':p', $initialValues['position'], PDO::PARAM_STR);
        $Q->bindValue(':c', $initialValues['container'], PDO::PARAM_INT);
        return $Q->execute() ? Widget::factory($DBH->lastInsertId()) : null;
    }

    public static function exists($id) {
        $dbh = Application::getDatabaseHandler();
        $q = $dbh->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::WIDGETS) . ' WHERE id=:i');
        $q->bindValue(':i', (int) $id, PDO::PARAM_INT);
        $q->execute();
        return $q->rowCount() ? true : false;
    }

    public static function getList($fetch = false) {
        // TODO: Implement getList() method.
        throw new \RuntimeException("Unsupported operation.");
    }

    public function isBrowseable() {
        // TODO: Implement isBrowseable() method.
        throw new \RuntimeException("Unsupported operation.");
    }

    public function actionGoTo() {
        // TODO: Implement actionGoTo() method.
        throw new \RuntimeException("Unsupported operation.");
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

    public function setVisible($visible) {
        $this->visible = (bool) $visible;
        return $this;
    }

    public function setScript($script) {
        $this->script = $script;
        return $this;
    }

    public function setPageToRender(Page $page) {
        $this->pageToRender = $page;
        return $this;
    }

    /**
     * @return Page
     */
    public function getPageToRender() {
        return $this->pageToRender;
    }


}
