<?php

namespace Alien\Table;


use Alien\Forms\Input;
use Alien\View;

class Table {

    protected $name;
    protected $description;
    protected $header;
    protected $rows;
    protected $options;
    protected $adminButtons = array();

    public function __construct(array $data) {
        $this->header = $data['header'];
        $this->rows = $data['data'];
        $this->viewSrc = "display/table/table.php";
    }

    public function __toString() {
        $header = $this->header;
        $rows = $this->rows;
        if (sizeof($this->adminButtons)) {
            $header[] = '';
            foreach ($rows as &$row) {
                $buttons = '';
                foreach ($this->adminButtons as $ab) {
                    switch ($ab['type']) {
                        case 'a':
                            $buttons .= '<a href="' . str_replace($ab['key'], $row['id'], $ab['href']) . '">' . $ab['text'] . '</a>';
                            break;
                    }
                }
                $row[] = $buttons;
            }
        }
        $view = new View($this->viewSrc);
        $view->name = $this->name;
        $view->description = $this->description;
        $view->header = $header;
        $view->rows = $rows;
        $view->options = $this->getOptions();
        return $view->renderToString();
    }

    public function setHeader(array $header) {
        $this->header = $header;
    }

    public function addRow(array $row) {
        $this->rows[] = $row;
    }

    public function addButton(array $button) {
        $this->adminButtons[] = $button;
    }

    protected function getOptions() {
        return array();
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

}