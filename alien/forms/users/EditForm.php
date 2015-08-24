<?php

namespace Alien\Form\Users;

use Alien\Form\Form;
use Alien\Form\Input;
use Alien\Form\Validator;
use Alien\Controllers\AbstractController;
use Alien\Models\Authorization\User;
use Alien\Models\Authorization\UserInterface;

class EditForm extends Form {

    private $user;

    public function __construct() {
        parent::__construct('post', '', 'editUserForm');
    }

    public static function factory(UserInterface $user) {
        parent::factory();
        $form = new EditForm();
        $form->addClass('form-horizontal');
        $form->user = $user;
        $form->setId('userForm');
        Input::hidden('action', 'user/edit/' . $user->getId())->addToForm($form);
        Input::hidden('userId', $user->getId())->addToForm($form);
        Input::text('userLogin', '', $user->getLogin())
             ->setAutocomplete(false)
             ->addValidator(new Validator\RequiredValidator('login nemôže byť prázdny'))
             ->addToForm($form);
        Input::text('userFirstname', '', $user->getFirstname())
             ->setAutocomplete(false)
             ->addValidator(new Validator\RequiredValidator('krstné meno nemôže ostať prázdne'))
             ->addToForm($form);
        Input::text('userSurname', '', $user->getSurname())
             ->setAutocomplete(false)
             ->addValidator(new Validator\RequiredValidator('priezvisko nemôže ostať prázdne'))
             ->addToForm($form);
        Input::text('userEmail', '', $user->getEmail())
             ->setAutocomplete(false)
             ->addValidator(new Validator\EmailValidator('neplatná emailová adresa'))
             ->addValidator(new Validator\CustomValidator('userUniqueEmail', array('ignoredUserId' => $user->getId()), 'tento email sa už používa'))
             ->addToForm($form);
        Input::password('userCurrentPass', '')->addToForm($form);
        Input::password('userPass2', '')
             ->setAutocomplete(false)
             ->addValidator((new Validator\LengthValidator(4, null, "'nové heslo musí mať aspoň 4 znaky'"))->setIsChainBreaking(false))
             ->addToForm($form);
        Input::password('userPass3', '')->addToForm($form);

        $status = Input::select('userStatus');
        $status->addOption(new Input\Option('aktívny', Input\Option::TYPE_SELECT, 1));
        $status->addOption(new Input\Option('neaktívny', Input\Option::TYPE_SELECT, 0));
        $status->addToForm($form);

        Input::button(AbstractController::staticActionURL('users', 'viewList'), 'Zrušiť', 'fa fa-arrow-circle-left')->addCssClass('btn-danger')->setName('buttonCancel')->addToForm($form);
        Input::button("javascript: $('#userForm').submit();", 'Uložiť', 'fa fa-check')->addCssClass('btn-success')->setName('buttonSave')->addToForm($form);
        Input::button(AbstractController::staticActionURL('dashboard', 'composeMessage', array('id' => $user->getId())), 'Poslať správu', 'fa fa-envelope')->addCssClass('btn-primary')->setName('buttonMessage')->addToForm($form);
        Input::button(AbstractController::staticActionURL('users', 'resetPassword', array('id' => $user->getId())), 'Resetovať heslo', 'fa fa-key')->setName('buttonResetPassword')->setDisabled(true)->addToForm($form);
        Input::button(AbstractController::staticActionURL('users', 'removeUser', array('id' => $user->getId())), 'Odstrániť používateľa', 'fa fa-times')->setName('buttonDelete')->setDisabled(true)->addToForm($form);

        Input::button('javascript: userShowAddGroupDialog(' . $user->getId() . ');', 'Pridať skupinu', 'icon-plus')->setName('buttonAddGroup')->addToForm($form);
        Input::button('javascript: userShowAddPermissionDialog(' . $user->getId() . ');', 'Pridať oprávnenie', 'icon-plus')->setName('buttonAddPermission')->addToForm($form);

        return $form;
    }

}
