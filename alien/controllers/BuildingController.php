<?php

namespace Alien\Controllers;

use Alien\Response;
use Alien\Table\DataTable;

class BuildingController extends BaseController {

    protected function initialize() {

        $this->defaultAction = 'view';

        parent::initialize();

//        $parentResponse = parent::initialize();
//        if ($parentResponse instanceof Response) {
//            $data = $parentResponse->getData();
//        }

        return new Response(array(
                'LeftTitle' => 'Budova',
                'ContentLeft' => $this->leftMenuItems(),
                'MainMenu' => ""
            )
        );
    }

    private function leftMenuItems() {
        $items = Array();
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('building', 'view'), 'img' => 'home', 'text' => 'Budovy');
        $items[] = Array('permissions' => null, 'url' => BaseController::actionURL('building', 'viewRooms'), 'img' => 'home', 'text' => 'Miestnosti');
        return $items;
    }

    protected function view() {

        $buildingDao = $this->getServiceManager()->getDao('BuildingDao');
        $data = $buildingDao->getTableData($buildingDao->getList());

        $table = new DataTable($data, array('ordering' => true));

        return new Response(array(
                'Title' => 'Zoznam budov',
                'ContentMain' => $table
            )
        );
    }

    protected function viewRooms() {

        $roomDao = $this->getServiceManager()->getDao('RoomDao');
        $data = $roomDao->getTableData($roomDao->getList());

        $table = new DataTable($data, array('ordering' => true));

        return new Response(array(
                'Title' => 'Zoznam miestností',
                'ContentMain' => $table
            )
        );
    }
}