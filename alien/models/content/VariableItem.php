<?php

namespace Alien\Models\Content;

use Alien\Application;
use Alien\DBConfig;
use \PDO;

class VariableItem extends Item {

    private $widgetContainer;
    private $pageToRender;
    private $containerId;

    public function __construct(Page $page, $containerId) {
        $this->pageToRender = $page;
        $this->containerId = $containerId;
        $this->fetchWidgets();
    }

    public function getType() {
        // TODO: Implement getType() method.
        throw new \RuntimeException("Unsupported operation.");
    }

    public static function getList($fetch = false) {
        // TODO: Implement getList() method.
        throw new \RuntimeException("Unsupported operation.");
    }

    public function update() {
        // TODO: Implement update() method.
        throw new \RuntimeException("Unsupported operation.");
    }

    public function isDeletable() {
        // TODO: Implement isDeletable() method.
        throw new \RuntimeException("Unsupported operation.");
    }

    public static function create($initialValues) {
        // TODO: Implement create() method.
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

    public function actionEdit() {
        // TODO: Implement actionEdit() method.
        throw new \RuntimeException("Unsupported operation.");
    }

    public function actionDrop() {
        // TODO: Implement actionDrop() method.
        throw new \RuntimeException("Unsupported operation.");
    }

    private function fetchWidgets() {
        if (!($this->widgetContainer instanceof WidgetContainer)) {
            $this->widgetContainer = new WidgetContainer();
        }
        $dbh = Application::getDatabaseHandler();
        foreach ($dbh->query('SELECT * FROM ' . DBConfig::table(DBConfig::WIDGETS) . ' WHERE page="' . $this->pageToRender->getId() . '" && container="' . $this->containerId . '"') as $row) {
            $this->widgetContainer->push(Widget::factory($row['id'], null, $row));
        }
    }

    /**
     * @return WidgetContainer
     */
    public function getWidgetContainer() {
        return $this->widgetContainer;
    }


}

