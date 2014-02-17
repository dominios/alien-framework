<?php
/**
 * Created by PhpStorm.
 * User: Domino
 * Date: 1.2.2014
 * Time: 0:06
 */

namespace Alien\Forms\Content;

use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Forms\Validator;
use Alien\Controllers\BaseController;
use Alien\Models\Content\Template;

class TemplateForm extends Form {

    private $template;

    public function __construct() {
        parent::__construct('post', '', 'editUserForm');
    }

    public static function create(Template $template){
        $form = new TemplateForm();
        $form->template = $template;
        $form->setId('templateForm');
        Input::hidden('action', 'template/edit')->addToForm($form);
        Input::hidden('templateId', $template->getId())->addToForm($form);
        Input::text('templateName', '', $template->getName())
            ->addValidator(Validator::custom('templateUniqueName', array('ignore' => $template->getId()), 'Zadaný názov šablóny sa už používa'))
            ->addToForm($form);
        Input::text('templateDescription', '', $template->getDescription())->addToForm($form);
        Input::text('templateSrc', '', $template->getSrcURL())->addToForm($form);
        Input::button('javascript: templateShowFileBrowser(\'php\');', '', 'icon-external-link')->setName('buttonSrcChoose')->addToForm($form);
        Input::button('javascript: templateShowFilePreview($(\'input[name=templateSrc]\').attr(\'value\'));', '', 'icon-magnifier')->setName('buttonSrcMagnify')->addToForm($form);
        Input::button(BaseController::actionURL('content', 'viewTemplates'), 'Zrušiť', 'icon-cancel')->addCssClass('negative')->setName('buttonCancel')->addToForm($form);
        Input::button('javascript: $(\'#templateForm\').submit();', 'Uložiť', 'icon-tick')->addCssClass('positive')->setName('buttonSubmit')->addToForm($form);
        return $form;
    }
} 