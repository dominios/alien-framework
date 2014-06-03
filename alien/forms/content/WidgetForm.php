<?php

namespace Alien\Forms\Content;

use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Forms\Input\Option;
use Alien\Forms\Validator;
use Alien\Controllers\BaseController;
use Alien\Models\Content\Template;
use Alien\Models\Content\Widget;

class WidgetForm extends Form {

    private $widget;

    public function __construct() {
        parent::__construct('post', '', 'editWidgetForm');
    }

    public static function factory(Widget $widget) {
        parent::factory();
        $form = new WidgetForm();
        $form->widget = $widget;
        $form = $widget->injectCustomFormElements($form);
        $form->setId('widgetForm');
        Input::hidden('action', 'content/editWidget')->addToForm($form);
        Input::hidden('widgetId', $widget->getId())->addToForm($form);

        $select = Input::select('widgetTemplate');
        $type = str_replace('Widget', '', $form->widget->getType());
        $files = glob('widgets/' . $type . '/*.{php,phtml}', GLOB_BRACE);
        foreach ($files as $file) {
            $opt = new Option($file, Option::TYPE_SELECT, $file);
            $select->addOption($opt);
            if ($form->widget->getScript() == $file) {
                $select->selectOption($opt);
            }
        }

        $select->addToForm($form);

        Input::checkbox('widgetVisibility', 'visible', $form->widget->isVisible())->addToForm($form);

//        Input::button(BaseController::actionURL('users', 'viewList'), 'Zrušiť', 'icon-back')->addCssClass('negative')->setName('buttonCancel')->addToForm($form);
        Input::button("javascript: $('#" . $form->getId() . "').submit();", 'Uložiť', 'icon-tick')->addCssClass('positive')->setName('buttonSave')->addToForm($form);

        return $form;
    }

}