<?php

namespace Alien\Forms\Content;

use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Forms\Validator;
use Alien\Controllers\BaseController;
use Alien\Models\Content\Page;

class PageForm extends Form {

    private $page;

    public function __construct() {
        parent::__construct('post', '', 'editPageForm');
    }

    public static function create(Page $page) {
        $form = new PageForm();
        $form->page = $page;
        $form->setId('pageForm');
        Input::hidden('action', 'page/edit')->addToForm($form);
        Input::hidden('pageId', $page->getId())->addToForm($form);
        Input::text('pageName', '', $page->getName())->addToForm($form);
        Input::text('pageSeolink', '', $page->getName())
             ->addValidator(Validator::custom('pageSeolink', array('ignore' => $page->getId()), 'Seolink musí byť unikátny.'))
             ->addToForm($form);
        Input::textarea('pageDescription', '', $page->getDescription())->addToForm($form);
        Input::textarea('pageKeywords', '', implode(' ', $page->getKeywords()))->addToForm($form);
        Input::hidden('pageTemplate', $page->getTemplate())->addToForm($form);
        Input::text('pageTemplateHelper', '', $page->getTemplate(true)->getName())
             ->setDisabled(true)
             ->addToForm($form);
        Input::button('javascript: pageShowTemplateBrowser();', '', 'icon-external-link')->setName('buttonTemplateChoose')->addToForm($form);
        Input::button('javascript: pageShowTemplatePreview();', '', 'icon-magnifier')->setName('buttonTemplatePreview')->addToForm($form);
        // "?content=editTemplate&id=<?= $this->Page->getTemplate();
        Input::button(BaseController::actionURL('page', 'viewAll'), 'Zrušiť', 'icon-cancel')->addCssClass('negative')->setName('buttonCancel')->addToForm($form);
        Input::button('javascript: $(\'#pageForm\').submit();', 'Uložiť', 'icon-tick')->addCssClass('positive')->setName('buttonSubmit')->addToForm($form);
        return $form;
    }
} 