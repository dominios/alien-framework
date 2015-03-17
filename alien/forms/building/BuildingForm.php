<?php
/**
 * Created by PhpStorm.
 * User: Domino
 * Date: 14.12.2014
 * Time: 15:33
 */

namespace Alien\Forms\Building;


use Alien\Controllers\BaseController;
use Alien\Forms\Fieldset;
use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Models\School\Building;
use Alien\Router;

class BuildingForm extends Form {

    /**
     * @var Building
     */
    private $building;

    public function __construct() {
        parent::__construct('post', '', 'buildingForm');
    }

    public static function factory(Building $building) {
        parent::factory();

        $form = new BuildingForm();
        $form->building = $building;
        $form->addClass('form-horizontal');

        $form->setId('buildingForm');

        Input::hidden('action', Router::getRouteUrl('building/edit/' . $building->getId()))->addToForm($form);
        Input::hidden('id', $building->getId())->addToForm($form);

        $buildingFieldset = new Fieldset('general');

        Input::text('buildingName', '', $building->getName())
             ->setLabel('Názov budovy')
             ->addToFieldset($buildingFieldset);

        Input::text('buildingStreet', '', $building->getStreet())
             ->setLabel('Ulica')
             ->addToFieldset($buildingFieldset);

        Input::text('buildingZip', '', $building->getZip())
             ->setLabel('PSČ')
             ->addToFieldset($buildingFieldset);

        Input::text('buildingCity', '', $building->getCity())
             ->setLabel('Mesto')
             ->addToFieldset($buildingFieldset);

        Input::text('buildingState', '', $building->getState())
             ->setLabel('Štát')
             ->addToFieldset($buildingFieldset);

        $submitFieldset = new Fieldset("submit");
        $submitFieldset->setViewSrc('display/common/submitFieldset.php');

        Input::button(Router::getRouteUrl('building'), 'Zrušiť')
             ->addCssClass('btn-danger')
             ->setName('buttonCancel')
             ->addToFieldset($submitFieldset);

        Input::button("javascript: $('#buildingForm').submit();", 'Uložiť')
             ->addCssClass('btn-success')
             ->setName('buttonSave')
             ->addToFieldset($submitFieldset);

        $form->addFieldset($buildingFieldset);
        $form->addFieldset($submitFieldset);

        return $form;

    }


} 