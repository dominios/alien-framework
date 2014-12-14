<?php

namespace Alien\Controllers;

use Alien\Db\RecordNotFoundException;
use Alien\Forms\Building\BuildingForm;
use Alien\Forms\Input;
use Alien\Models\School\Building;
use Alien\Models\School\BuildingDao;
use Alien\Response;
use Alien\Table\DataTable;
use Alien\View;

class BuildingController extends BaseController {

    /**
     * @var BuildingDao
     */
    private $buildingDao;

    protected function initialize() {

        $this->defaultAction = 'view';

        parent::initialize();

        $this->buildingDao = $this->getServiceManager()->getDao('BuildingDao');

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
        $items[] = Array('permissions' => null, 'url' => BaseController::staticActionURL('building', 'view'), 'img' => 'home', 'text' => 'Budovy');
        $items[] = Array('permissions' => null, 'url' => BaseController::staticActionURL('building', 'viewRooms'), 'img' => 'home', 'text' => 'Miestnosti');
        $items[] = Array('permissions' => null, 'url' => BaseController::staticActionURL('schedule', 'view', array('interval' => 'week')), 'img' => 'home', 'text' => 'Rozvrh');
        return $items;
    }

    protected function view() {

        $view = new View('display/building/view.php');

        $buildingDao = $this->buildingDao;
        $data = $buildingDao->getTableData($buildingDao->getList());

        $table = new DataTable($data, array('ordering' => true));

        $table->addButton(array(
            'type' => 'a',
            'text' => '[Upraviť]',
            'class' => '',
            'key' => '%id%',
            'href' => $this->actionUrl('edit', array('id' => '%id%'))
        ));

        $table->addButton(array(
            'type' => 'a',
            'text' => '[Vymazať]',
            'class' => '',
            'key' => '%id%',
            'href' => $this->actionUrl('remove', array('id' => '%id%'))
        ));

        $view->table = $table;

        $addButton = Input::button($this->actionUrl('addBuilding'), 'Pridať budovu');
        $view->addButton = $addButton;

        return new Response(array(
                'Title' => 'Zoznam budov',
                'ContentMain' => $view->renderToString()
            )
        );
    }

    protected function edit() {

        $building = $this->buildingDao->find($_GET['id']);
        if (!($building instanceof Building)) {
            throw new RecordNotFoundException();
        }

        $view = new View('display/building/form.php');
        $form = BuildingForm::factory($building);
        $form->getField('action', true)->setValue('building/edit');

        if ($form->isPostSubmit()) {
            if ($form->validate()) {
                $building->setName($_POST['buildingName'])
                         ->setCity($_POST['buildingCity'])
                         ->setState($_POST['buildingState'])
                         ->setStreet($_POST['buildingStreet'])
                         ->setZip($_POST['buildingZip']);
                $this->buildingDao->update($building);
                $this->redirect($this->actionUrl('view'));
            }
        }

        $view->form = $form;

        return new Response(array(
                'Title' => 'Upraviť budovu',
                'ContentMain' => $view
            )
        );
    }

    protected function remove() {
        $building = $this->buildingDao->find($_GET['id']);
        if ($building instanceof Building) {
            $this->buildingDao->delete($building);
        }
        $this->redirect($this->actionUrl('view'));
    }

    protected function addBuilding() {

        $view = new View('display/building/form.php');
        $building = new Building();
        $form = BuildingForm::factory($building);

        if ($form->isPostSubmit()) {
            if ($form->validate()) {
                $building->setName($_REQUEST['buildingName'])
                         ->setCity($_REQUEST['buildingCity'])
                         ->setState($_REQUEST['buildingState'])
                         ->setStreet($_REQUEST['buildingStreet'])
                         ->setZip($_REQUEST['buildingZip']);
                $this->buildingDao->create($building);
                $this->redirect($this->actionUrl('view'));
            }
        }

        $view->form = $form;

        return new Response(array(
                'Title' => 'Pridať budovu',
                'ContentMain' => $view
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