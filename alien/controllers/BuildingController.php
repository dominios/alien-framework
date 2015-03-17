<?php

namespace Alien\Controllers;

use Alien\Db\RecordNotFoundException;
use Alien\Forms\Building\BuildingForm;
use Alien\Forms\Building\RoomForm;
use Alien\Forms\Input;
use Alien\Models\Authorization\Authorization;
use Alien\Models\Authorization\UserDao;
use Alien\Models\School\Building;
use Alien\Models\School\BuildingDao;
use Alien\Models\School\Room;
use Alien\Models\School\RoomDao;
use Alien\Notification;
use Alien\Response;
use Alien\Router;
use Alien\Table\DataTable;
use Alien\View;

class BuildingController extends BaseController {

    /**
     * @var Authorization
     */
    private $authorization;

    /**
     * @var BuildingDao
     */
    private $buildingDao;

    /**
     * @var UserDao
     */
    private $userDao;

    /**
     * @var RoomDao
     */
    private $roomDao;

    protected function initialize() {

        $this->defaultAction = 'listAction';

        parent::initialize();

        $this->authorization = $this->getServiceManager()->getService('Authorization');
        $this->buildingDao = $this->getServiceManager()->getDao('BuildingDao');
        $this->userDao = $this->getServiceManager()->getDao('UserDao');
        $this->roomDao = $this->getServiceManager()->getDao('RoomDao');

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
//        $items[] = Array('permissions' => null, 'url' => BaseController::staticActionURL('schedule', 'view', array('interval' => 'week')), 'img' => 'home', 'text' => 'Rozvrh');
        return $items;
    }

    protected function listAction() {

        $buildingDao = $this->buildingDao;
        $data = $buildingDao->getTableData($buildingDao->getList());

        $table = new DataTable($data, array('ordering' => true));
        $table->setName('Zoznam budov');
        if ($this->authorization->getCurrentUser()->hasPermission('BUILDING_ADMIN')) {
            $table->addHeaderColumn(array('edit' => ''));
            $table->addRowColumn(array(
                'edit' => function ($row) {
                        $ret = "";
                        $urlEdit = Router::getRouteUrl('building/edit/' . $row['id']);
                        $urlRemove = Router::getRouteUrl('building/remove/' . $row['id']);
                        $ret .= "<a href=\"$urlEdit\"><i class=\"fa fa-pencil\"></i></a>";
                        $ret .= " <a href=\"$urlRemove\"><i class=\"fa fa-trash-o text-danger\"></i></a>";
                        return $ret;
                    }
            ));
        };

        $this->view->table = $table;

        $addButton = Input::button($this->actionUrl('addBuilding'), 'Pridať budovu')->addCssClass('btn-primary')->setIcon('fa fa-plus');
        $this->view->addButton = $addButton;

        return new Response(array(
                'Title' => 'Zoznam budov',
                'ContentMain' => $this->view
            )
        );
    }

    protected function editAction() {

        $building = $this->buildingDao->find($this->getParam('id'));
        if (!($building instanceof Building)) {
            throw new RecordNotFoundException();
        }

        $form = BuildingForm::factory($building);
//        $form->getField('action', true)->setValue('building/editBuilding');

        $this->view->building = $building;

        if ($form->isPostSubmit()) {
            if ($form->validate()) {
                $building->setName($_POST['buildingName'])
                         ->setCity($_POST['buildingCity'])
                         ->setState($_POST['buildingState'])
                         ->setStreet($_POST['buildingStreet'])
                         ->setZip($_POST['buildingZip']);
                $this->buildingDao->update($building);
                Notification::success('Zmeny boli uložené');
                $this->redirect($this->actionUrl(''));
            }
        }

        $this->view->form = $form;

        return new Response(array(
                'Title' => 'Upraviť budovu',
                'ContentMain' => $this->view
            )
        );
    }

    protected function removeBuildingAction() {
        $building = $this->buildingDao->find($this->getParam('id'));
        if ($building instanceof Building) {
            $this->buildingDao->delete($building);
        }
        $this->redirect($this->actionUrl(''));
    }

    protected function addBuildingAction() {

        $view = new View('display/building/editAction.php');

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
                Notification::success('Záznam pridaný');
                $this->redirect($this->actionUrl(''));
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

        $view = new View('display/room/view.php');

        $roomDao = $this->getServiceManager()->getDao('RoomDao');
        $data = $roomDao->getTableData($roomDao->getList());

        $table = new DataTable($data, array('ordering' => true));

        $table->addButton(array(
            'type' => 'a',
            'text' => '[Upraviť]',
            'class' => '',
            'key' => '%id%',
            'href' => $this->actionUrl('editRoom', array('id' => '%id%'))
        ));

        $table->addButton(array(
            'type' => 'a',
            'text' => '[Vymazať]',
            'class' => '',
            'key' => '%id%',
            'href' => $this->actionUrl('removeRoom', array('id' => '%id%'))
        ));

        $view->table = $table;

        $addButton = Input::button($this->actionUrl('addRoom'), 'Pridať miestnosť');
        $view->addButton = $addButton;

        return new Response(array(
                'Title' => 'Zoznam miestností',
                'ContentMain' => $view
            )
        );
    }

    protected function addRoom() {

        $room = new Room();

        $view = new View('display/room/form.php');
        $form = RoomForm::factory($room, $this->userDao, $this->buildingDao);
        $form->getField('action', true)->setValue('building/addRoom');

        if ($form->isPostSubmit()) {
            if ($form->validate()) {

                $building = $this->buildingDao->find($_POST['roomBuilding']);
                $responsible = $this->userDao->find($_POST['roomResponsible']);

                $room->setBuilding($building)
                     ->setCapacity($_POST['roomCapacity'])
                     ->setFloor($_POST['roomFloor'])
                     ->setNumber($_POST['roomFloor'])
                     ->setResponsible($responsible);
                $this->roomDao->create($room);
                $this->roomDao->update($room);
                Notification::success('Záznam pridaný');
                $this->redirect($this->actionUrl('viewRooms'));
            }
        }

        $view->form = $form;

        return new Response(array(
                'Title' => 'Pridať miestnosť',
                'ContentMain' => $view
            )
        );
    }

    protected function editRoom() {

        try {

            $room = $this->roomDao->find($_GET['id']);

            $view = new View('display/room/form.php');
            $form = RoomForm::factory($room, $this->userDao, $this->buildingDao);
            $form->getField('action', true)->setValue('building/editRoom');

            if ($form->isPostSubmit()) {
                if ($form->validate()) {
                    $room->setBuilding($this->buildingDao->find($_POST['roomBuilding']))
                         ->setCapacity($_POST['roomCapacity'])
                         ->setFloor($_POST['roomFloor'])
                         ->setNumber($_POST['roomFloor'])
                         ->setResponsible($this->userDao->find($_POST['roomResponsible']));
                    $this->roomDao->update($room);
                    Notification::success('Zmeny boli uložené');
                    $this->redirect($this->actionUrl('viewRooms'));
                }
            }

            $view->form = $form;

            return new Response(array(
                    'Title' => 'Upraviť miestnosť',
                    'ContentMain' => $view
                )
            );

        } catch (RecordNotFoundException $e) {
            Notification::warning('Room not found');
            $this->redirect($this->actionUrl('viewRooms'));
        }
    }

    protected function removeRoom() {
        $room = $this->roomDao->find($_GET['id']);
        if ($room instanceof Room) {
            $this->roomDao->delete($room);
        }
        $this->redirect($this->actionUrl('viewRooms'));
    }
}