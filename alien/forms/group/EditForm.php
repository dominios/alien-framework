<?php

namespace Alien\Forms\Group;

use Alien\Forms\Fieldset;
use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Forms\Validator;
use Alien\Controllers\BaseController;
use Alien\Models\Authorization\Group;

class EditForm extends Form {

    /**
     * @var Group
     */
    private $group;

    public function __construct() {
        parent::__construct('post', '', 'editGroupForm');
    }

    public static function factory(Group $group) {
        parent::factory();
        $form = new EditForm();
        $form->group = $group;
        $form->setId('groupForm');

        Input::hidden('action', 'group/edit')->addToForm($form);
        Input::hidden('groupId', $group->getId())->addToForm($form);

        $groupFieldset = new Fieldset("group");

        $submitFieldset = new Fieldset("submit");
        $submitFieldset->setViewSrc('display/common/submitFieldset.php');

        Input::text('groupName', '', $group->getName())
             ->setLabel("Názov")
             ->setAutocomplete(false)
             ->addValidator(new Validator\RequiredValidator('povinný parameter'))
             ->addToFieldset($groupFieldset);

        Input::text('groupDescription', '', $group->getDescription())
             ->setLabel("Popis")
             ->setAutocomplete(false)
             ->addToFieldset($groupFieldset);

        Input::button(BaseController::staticActionURL('group', 'view'), 'Zrušiť', 'icon-back')
             ->addCssClass('negative')
             ->setName('buttonCancel')
             ->addToFieldset($submitFieldset);

        Input::button("javascript: $('#groupForm').submit();", 'Uložiť', 'icon-tick')
             ->addCssClass('positive')
             ->setName('buttonSave')
             ->addToFieldset($submitFieldset);

        Input::button(BaseController::staticActionURL('group', 'remove', array('id' => $_GET['id'])), 'Odstrániť používateľa', 'icon-delete')
             ->setName('buttonDelete')
             ->setDisabled(true)
             ->addToFieldset($submitFieldset);

        $form->addFieldset($groupFieldset);
        $form->addFieldset($submitFieldset);

//        Input::button('javascript: userShowAddGroupDialog(' . $group->getId() . ');', 'Pridať skupinu', 'icon-plus')->setName('buttonAddGroup')->addToForm($form);
//        Input::button('javascript: userShowAddPermissionDialog(' . $group->getId() . ');', 'Pridať oprávnenie', 'icon-plus')->setName('buttonAddPermission')->addToForm($form);

        return $form;
    }

}
