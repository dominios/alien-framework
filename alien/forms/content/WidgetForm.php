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
        $form->widget = $widget;
        $form->setId('widgetForm');
        Input::hidden('action', 'template/edit')->addToForm($form);
        Input::hidden('widgetId', $widget->getId())->addToForm($form);
        return $form;
    }

}