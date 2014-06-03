<?php

namespace Alien\Forms\Content;

use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Controllers\BaseController;
use Alien\Models\Content\TextItem;

class TextItemForm extends Form {

    private $item;

    public function __construct() {
        parent::__construct('post', '', 'editItemForm');
    }

    public static function factory(TextItem $item) {
        parent::factory();
        $form = new TextItemForm();
        $form->item = $item;
        $form->setId('textItemForm');
        Input::hidden('action', 'textItem/edit')->addToForm($form);
        Input::hidden('itemId', $item->getId())->addToForm($form);
        Input::text('itemName', '', $item->getName())->addToForm($form);
        Input::textarea('itemContent', '', $item->getContent())
             ->addCssClass('ckeditor')
             ->addToForm($form);
        Input::button(BaseController::getRefererActionURL(), 'Zrušiť', 'icon-cancel')->addCssClass('negative')->setName('buttonCancel')->addToForm($form);
        Input::button('javascript: $(\'#editItemForm\').submit();', 'Uložiť', 'icon-tick')->addCssClass('positive')->setName('buttonSubmit')->addToForm($form);
        return $form;
    }
} 