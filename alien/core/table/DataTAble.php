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
    protected $ordering = true;

    public function __construct($data, array $options = null) {
        parent::__construct($data);
        $this->viewSrc = "display/table/dataTable.php";
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

}