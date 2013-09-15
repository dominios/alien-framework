<?php

abstract class ContentItemView {

    const Icon = 'file.png';
    const Name = 'Zobrazovač';
    const Type = 'ItemView';

    protected $id;
    protected $id_c;
    protected $type;
    protected $item;
    protected $position;
    protected $visible;
    protected $params;
    protected $page;
    protected $template;

    public function __construct($id, $row = null) {

        if ($row === null) {
            $DBH = Alien::getDatabaseHandler();
            $Q = $DBH->prepare('SELECT * FROM ' . Alien::getDBPrefix() . '_content_views WHERE id_v = :i');
            $Q->bindValue(':i', $id, PDO::PARAM_INT);
            $Q->execute();
            if (!$Q->rowCount()) {
                return;
            }
            $row = $Q - fetch();
        }

        $this->id = $row['id_v'];
        $this->id_c = $row['id_c'];
        $this->type = $row['id_type'];
        $this->item = $row['id_i'];
        $this->position = (int) $row['position'];
        $this->visible = (bool) $row['visible'];
        $this->params = unserialize($row['params']);
        $this->page = $row['id_p'];
        $this->template = $row['id_t'];
    }

    public static final function getSpecificView($idView, $idType = null, $R = null) {

        if ($idType !== null) {
            $cond = 'id_type = ' . $idType;
        } else {
            $cond = 'id_v = ' . (int) $idView;
        }
        $DBH = Alien::getDatabaseHandler();
        $row = $DBH->query('SELECT classname FROM ' . Alien::getDBPrefix() . '_content_item_types JOIN ' . Alien::getDBPrefix() . '_content_views USING (id_type) WHERE ' . $cond . ' LIMIT 1')->fetch();
        if (sizeof($row) && $row !== null) {
            $classname = $row['classname'] . 'View';
            if (class_exists($classname)) {
//                var_dump($classname); die;
//                return $R === null ? new $classname($idView) : new $classname(null, $R);
                return new $classname($idView, $R);
            }
        } else {
            return null;
        }
    }

    public abstract function getIcon();

    public abstract function getName();

    public abstract function getType();

    public function getId() {
        return $this->id;
    }

    public function getIdContainer() {
        return $this->id_c;
    }

    public function getParams() {
        return $this->params;
    }

    public function getPosition() {
        return $this->position;
    }

    public function getItem($fetch = false) {
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
            if ($this->item instanceof ContentTemplate) {
                return $this->template;
            } else {
                $this->item = ContentTemplate($this->template);
                return $this->template;
            }
        } else {
            if ($this->item instanceof ContentTemplate) {
                return $this->template->getId();
            } else {
                return $this->template;
            }
        }
    }

    public function actionEdit() {
        return AlienController::actionURL('content', 'editView', array('id' => $this->id));
    }

    public function actionDrop() {
        return AlienController::actionURL('content', 'dropView', array('id' => $this->id));
    }

}
