<?php
namespace Alien\Table;

use Alien\View;

/**
 * Class DataTable
 * @package Alien\Table
 * @see https://datatables.net
 */
class DataTable extends Table {

    protected $paging = false;
    protected $info = false;
    protected $searching = false;
    protected $ordering = false;

    public function __construct($data, array $options = null) {
        parent::__construct($data);
        if (is_array($options)) {
            $rc = new \ReflectionClass($this);
            foreach ($options as $k => $v) {
                if ($rc->hasProperty($k)) {
                    $this->$k = $v;
                }
            }
        }
    }

    protected function getOptions() {
        return (array(
            'paging' => $this->paging,
            'info' => $this->info,
            'searching' => $this->searching,
            'ordering' => $this->ordering
        ));
    }

    public function __toString() {
        $view = new View("display/table/dataTable.php");
        $view->header = $this->header;
        $view->rows = $this->rows;
        $view->options = $this->getOptions();
        return $view->renderToString();
    }

}