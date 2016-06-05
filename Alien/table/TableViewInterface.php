<?php

namespace Alien\Table;

interface TableViewInterface {

    public function getTableHeader();

    public function getTableRowData($object = null);

    public function getTableData(array $array);
}