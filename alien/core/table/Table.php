<?php

namespace Alien\Table;


use Alien\View;

class Table {

    protected $name;
    protected $description;
    protected $header;
    protected $rows;

    public function __construct(array $data) {
        $this->header = $data['header'];
        $this->rows = $data['data'];
    }

    public function __toString() {
        $view = new View("display/table/table.php");
        $view->header = $this->header;
        $view->rows = $this->rows;
        return $view->renderToString();
    }

    public function setHeader(array $header) {
        $this->header = $header;
    }

    public function addRow(array $row) {
        $this->rows[] = $row;
    }
}