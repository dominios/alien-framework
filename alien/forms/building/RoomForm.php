<?php

namespace Alien\Forms\Building;


use Alien\Controllers\BaseController;
use Alien\Forms\Fieldset;
use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Forms\Input\Option;
use Alien\Models\Authorization\User;
use Alien\Models\Authorization\UserDao;
use Alien\Models\School\Building;
use Alien\Models\School\BuildingDao;
use Alien\Models\School\Room;

class RoomForm extends Form {

    /**
     * @var Room
     */
    private $room;

    /**
     * @var UserDao
     */
    private $userDao;

    /**
     * @var BuildingDao
     */
    private $buildingDao;

    public function __construct() {
        parent::__construct('post', '', 'roomForm');
    }

    /**
     * @param Room $room
     */
    public static function factory(Room $room, UserDao $userDao, BuildingDao $buildingDao) {
        parent::factory();

        $form = new RoomForm();
        $form->userDao = $userDao;
        $form->buildingDao = $buildingDao;
        $form->room = $room;

        $form->setId('roomForm');

        Input::hidden('action', 'building/addRoom')->addToForm($form);
        Input::hidden('id', $room->getId())->addToForm($form);

        $generalFieldset = new Fieldset('general');

        $building = Input::select('roomBuilding')
                         ->setLabel('Budova')
                         ->addToFieldset($generalFieldset);;
        foreach ($form->buildingDao->getList() as $i) {
            $opt = new Option($i->getName() . ' ' . $i->getStreet(), Option::TYPE_SELECT, $i->getId());
            if ($room->getBuilding() instanceof Building) {
                if ($room->getBuilding()->getId() == $i->getId()) {
                    $opt->setSelected(true);
                }
            }
            $building->addOption($opt);
        }

        Input::text('roomFloor', '', $room->getFloor())
             ->setLabel('Poshodie')
             ->addToFieldset($generalFieldset);

        Input::text('roomNumber', '', $room->getNumber())
             ->setLabel('Číslo dverí')
             ->addToFieldset($generalFieldset);

        Input::text('roomCapacity', '', $room->getCapacity())
             ->setLabel('Kapacita')
             ->addToFieldset($generalFieldset);

        $responsible = Input::select('roomResponsible')
                            ->setLabel('Zodpovedný')
                            ->addToFieldset($generalFieldset);;
        foreach ($form->userDao->getList() as $i) {
            $opt = new Option($i->getLogin(), Option::TYPE_SELECT, $i->getId());
            if ($room->getResponsible() instanceof User) {
                if ($room->getResponsible()->getId() == $i->getId()) {
                    $opt->setSelected(true);
                }
            }
            $responsible->addOption($opt);
        }

        $submitFieldset = new Fieldset("submit");
        $submitFieldset->setViewSrc('display/common/submitFieldset.php');

        Input::button(BaseController::staticActionURL('building', 'view'), 'Zrušiť')
             ->addCssClass('negative')
             ->setName('buttonCancel')
             ->addToFieldset($submitFieldset);

        Input::button("javascript: $('#roomForm').submit();", 'Uložiť')
             ->addCssClass('positive')
             ->setName('buttonSave')
             ->addToFieldset($submitFieldset);

        $form->addFieldset($generalFieldset);
        $form->addFieldset($submitFieldset);

        return $form;

    }


} 