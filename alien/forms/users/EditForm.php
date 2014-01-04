<?php

namespace Alien\Forms\Users;

use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Forms\Validator;
use Alien\Controllers\BaseController;

class EditForm extends Form {

    private $user;

    public function __construct() {
        parent::__construct('post', '', 'editUserForm');
    }

    public static function create($user) {

        $form = new self();
        $form->user = $user;
        $form->setId('userForm');
        Input::hidden('action', 'users/edit')->addToForm($form);
        Input::hidden('userId', $user->getId())->addToForm($form);
        Input::text('userLogin', '', $user->getLogin())->setAutocomplete(false)->addToForm($form);
        Input::text('userFirstname', '', $user->getFirstname())->setAutocomplete(false)->addToForm($form);
        Input::text('userSurname', '', $user->getSurname())->setAutocomplete(false)->addToForm($form);
        Input::text('userEmail', '', $user->getEmail())
                ->setAutocomplete(false)
                ->addValidator(Validator::regexp(Validator::PATTERN_EMAIL, 'neplatná adresa'))
                ->addValidator(Validator::custom('userUniqueEmail', array('ignoredUserId' => $user->getId()), 'tento email sa už používa'))
                ->addToForm($form);
        Input::password('userPass2', '')->addToForm($form);
        Input::password('userPass3', '')->addToForm($form);

        Input::button(BaseController::actionURL('users', 'viewList'), 'Zrušiť', 'icon-back')->addCssClass('negative')->setName('buttonCancel')->addToForm($form);
        Input::button("javascript: $('#userForm').submit();", 'Uložiť', 'icon-tick')->addCssClass('positive')->setName('buttonSave')->addToForm($form);
        Input::button(BaseController::actionURL('dashboard', 'composeMessage', array('id' => $_GET['id'])), 'Poslať správu', 'icon-message')->setName('buttonMessage')->addToForm($form);
        Input::button(BaseController::actionURL('users', 'resetPassword', array('id' => $_GET['id'])), 'Resetovať heslo', 'icon-shield')->setName('buttonResetPassword')->setDisabled(true)->addToForm($form);
        Input::button(BaseController::actionURL('users', 'removeUser', array('id' => $_GET['id'])), 'Odstrániť používateľa', 'icon-delete')->setName('buttonDelete')->setDisabled(true)->addToForm($form);

        Input::button('javascript: userShowAddGroupDialog(' . $user->getId() . ');', 'Pridať skupinu', 'icon-plus')->setName('buttonAddGroup')->addToForm($form);
        Input::button('javascript: userShowAddPermissionDialog(' . $user->getId() . ');', 'Pridať oprávnenie', 'icon-plus')->setName('buttonAddPermission')->addToForm($form);

        return $form;
    }

}