<?php

namespace Alien\Forms\Users;

use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Forms\Validator;
use Alien\Controllers\BaseController;
use Alien\Models\Authorization\User;

class EditForm extends Form {

    private $user;

    public function __construct() {
        parent::__construct('post', '', 'editUserForm');
    }

    public static function factory(User $group) {
        parent::factory();
        $form = new EditForm();
        $form->user = $group;
        $form->setId('userForm');
        Input::hidden('action', 'users/edit')->addToForm($form);
        Input::hidden('userId', $group->getId())->addToForm($form);
        Input::text('userLogin', '', $group->getLogin())
             ->setAutocomplete(false)
             ->addValidator(new Validator\RequiredValidator('login nemôže byť prázdny'))
             ->addToForm($form);
        Input::text('userFirstname', '', $group->getFirstname())
             ->setAutocomplete(false)
             ->addValidator(new Validator\RequiredValidator('krstné meno nemôže ostať prázdne'))
             ->addToForm($form);
        Input::text('userSurname', '', $group->getSurname())
             ->setAutocomplete(false)
             ->addValidator(new Validator\RequiredValidator('priezvisko nemôže ostať prázdne'))
             ->addToForm($form);
        Input::text('userEmail', '', $group->getEmail())
             ->setAutocomplete(false)
             ->addValidator(new Validator\EmailValidator('neplatná emailová adresa'))
             ->addValidator(new Validator\CustomValidator('userUniqueEmail', array('ignoredUserId' => $group->getId()), 'tento email sa už používa'))
             ->addToForm($form);
        Input::password('userCurrentPass', '')->addToForm($form);
        Input::password('userPass2', '')
             ->setAutocomplete(false)
             ->addValidator(new Validator\LengthValidator(4, null, "'nové heslo musí mať aspoň 4 znaky'"))
             ->addToForm($form);
        Input::password('userPass3', '')->addToForm($form);

        Input::button(BaseController::staticActionURL('users', 'viewList'), 'Zrušiť', 'icon-back')->addCssClass('negative')->setName('buttonCancel')->addToForm($form);
        Input::button("javascript: $('#userForm').submit();", 'Uložiť', 'icon-tick')->addCssClass('positive')->setName('buttonSave')->addToForm($form);
        Input::button(BaseController::staticActionURL('dashboard', 'composeMessage', array('id' => $_GET['id'])), 'Poslať správu', 'icon-message')->setName('buttonMessage')->addToForm($form);
        Input::button(BaseController::staticActionURL('users', 'resetPassword', array('id' => $_GET['id'])), 'Resetovať heslo', 'icon-shield')->setName('buttonResetPassword')->setDisabled(true)->addToForm($form);
        Input::button(BaseController::staticActionURL('users', 'removeUser', array('id' => $_GET['id'])), 'Odstrániť používateľa', 'icon-delete')->setName('buttonDelete')->setDisabled(true)->addToForm($form);

        Input::button('javascript: userShowAddGroupDialog(' . $group->getId() . ');', 'Pridať skupinu', 'icon-plus')->setName('buttonAddGroup')->addToForm($form);
        Input::button('javascript: userShowAddPermissionDialog(' . $group->getId() . ');', 'Pridať oprávnenie', 'icon-plus')->setName('buttonAddPermission')->addToForm($form);

        return $form;
    }

}
