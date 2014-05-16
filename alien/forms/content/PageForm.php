<?php

namespace Alien\Forms\Content;

use Alien\Forms\Fieldset;
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
        $configFieldset = new Fieldset("config");
        $submitFieldset = new Fieldset("submit");
        $submitFieldset->setViewSrc('display/common/submitFieldset.php');

        $form->page = $page;
        $form->setId('pageForm');

        Input::hidden('action', 'page/edit')->addToForm($form);
        Input::hidden('pageId', $page->getId())->addToForm($form);

        Input::text('pageName', '', $page->getName())
             ->setLabel('Názov stránky')
             ->setIcon('icon-page')
             ->addToFieldset($configFieldset);

        Input::text('pageSeolink', '', $page->getName())
             ->addValidator(Validator::custom('pageSeolink', array('ignore' => $page->getId()), 'Seolink musí byť unikátny.'))
             ->setIcon('icon-link')
             ->setLabel('Seolink')
             ->addToFieldset($configFieldset);

        Input::textarea('pageDescription', '', $page->getDescription())
             ->setIcon('icon-note')
             ->setLabel('Popis stránky')
             ->addToFieldset($configFieldset);

        Input::textarea('pageKeywords', '', implode(' ', $page->getKeywords()))
             ->setIcon('icon-template')
             ->setLabel('Kľúčové slová')
             ->addToFieldset($configFieldset);

        $templateField = Input::text('pageTemplateHelper', '', $page->getTemplate(true)->getName())
            ->setIcon('icon-template')
            ->setLabel('Šablóna stránky')
            ->setDisabled(true)
            ->addToFieldset($configFieldset);

        Input::hidden('pageTemplate', $page->getTemplate())
             ->addToForm($form)
             ->addToFieldset($configFieldset);

        Input::button('javascript: pageShowTemplateBrowser();', '', 'icon-external-link')
             ->setName('buttonTemplateChoose')
             ->linkTo($templateField);

        Input::button('javascript: pageShowTemplatePreview();', '', 'icon-magnifier')
             ->setName('buttonTemplatePreview')
             ->linkTo($templateField);

        Input::button(BaseController::getRefererActionURL(), 'Zrušiť', 'icon-cancel')
             ->addCssClass('negative')
             ->setName('buttonCancel')
             ->addToFieldset($submitFieldset);

        Input::button('javascript: $(\'#pageForm\').submit();', 'Uložiť', 'icon-tick')
             ->addCssClass('positive')
             ->setName('buttonSubmit')
             ->addToFieldset($submitFieldset);

        $form->addFieldset($configFieldset);
        $form->addFieldset($submitFieldset);

        return $form;
    }
} 