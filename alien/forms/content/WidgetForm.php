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

    public static function create(Widget $widget) {
        $form = new self();
        $form = $widget->injectCustomFormElements($form);
        $form->widget = $widget;
        $form->setId('widgetForm');
        Input::hidden('action', 'template/edit')->addToForm($form);
        Input::hidden('widgetId', $widget->getId())->addToForm($form);

        $select = Input::select('widgetTemplate');
        $type = str_replace('Widget', '', $form->widget->getType());
        $files = glob('widgets/' . $type . '/*.{php,phtml}', GLOB_BRACE);
        foreach ($files as $file) {
            $opt = new Option($file, Option::TYPE_SELECT, $file);
            $select->addOption($opt);
            if ($form->widget->getTemplate(false) === $file) {
                $select->selectOption($opt);
            }
        }
        $select->addToForm($form);

        Input::checkbox('widgetVisibility', 'visible', $form->widget->isVisible())->addToForm($form);

        return $form;
    }

}