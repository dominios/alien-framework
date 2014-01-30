<?php

namespace Alien\Controllers;

use Alien\Alien;
use Alien\View;
use Alien\Response;
use Alien\Notification;
use Alien\Models\Content\Folder;
use Alien\Models\Content\Template;
use Alien\Models\Content\TemplateBlock;
use Alien\Models\Content\Page;
use Alien\Models\Content\Widget;
use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Forms\Validator;
use PDO;

class TemplateController extends ContentController {

    protected function viewAll() {
        return $this->viewList('template');
    }

    protected function edit() {

        if (!preg_match('/^[0-9]*$/', $_GET['id'])) {
            Notification::ERROR('Neplatný identifikátor šablóny.');
            return;
        }

        $view = new View('display/content/temlateForm.php', $this);

        $template = new Template((int) $_GET['id']);
//        $template->fetchViews();

        $form = new Form('post', '', 'templateForm');
        $inputAction = Input::hidden('action', BaseController::actionURL('content', 'editTemplate'))->addToForm($form);
        $inputTemplateId = Input::hidden('templateId', '', $template->getId())->addToForm($form);
        $inputName = Input::text('templateName', '', $template->getName())->addToForm($form);
        $inputDescription = Input::text('templateDescription', '', $template->getDescription())->addToForm($form);
        $inputSrc = Input::text('templateSrc', '', $template->getSrcURL())->addToForm($form);
        $buttonSrcChoose = Input::button('javascript: templateShowFileBrowser(\'php\');', '', 'icon-external-link');
        $buttonSrcMagnify = Input::button('javascript: templateShowFilePreview($(\'input[name=templatePhp]\').attr(\'value\'));', '', 'icon-magnifier');
        $buttonCancel = Input::button(BaseController::actionURL('content', 'viewTemplates'), 'Zrušiť', 'icon-cancel')->addCssClass('negative');
        $buttonSubmit = Input::button('javascript: $(\'#templateForm\').submit();', 'Uložiť', 'icon-tick')->addCssClass('positive');
        $view->buttonCancel = $buttonCancel;
        $view->buttonSubmit = $buttonSubmit;
        $view->buttonSrcChoose = $buttonSrcChoose;
        $view->buttonSrcMagnify = $buttonSrcMagnify;

        $view->returnAction = BaseController::actionURL('content', 'browser', array('folder' => $_SESSION['folder']));
        $view->template = $template;
        $view->form = $form;

        $viewFloatPanel = new View('display/content/partial/templateToolBox.php');

        return new Response(Response::OK, Array(
            'Title' => 'Úprava šablóny: ' . $template->getName(),
            'ContentMain' => $view->renderToString(),
            'FloatPanel' => $viewFloatPanel->renderToString()
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    protected function templateFormSubmit() {
        $id = $_POST['templateId'];
        $nazov = $_POST['templateName'];
        $php = $_POST['templatePhp'];
        $ini = $_POST['templateIni'];
        $css = $_POST['templateCss'];
        if (!preg_match('/^[0-9]*$/', $id)) {
            return;
        }
        if (!strlen($nazov)) {
            FormValidator::getInstance()->putError('templateName', 'Názov šablóny nemôže ostať prázdny.');
        }
        if (!file_exists($ini)) {
            FormValidator::getInstance()->putError('templateIni', 'Konfiguračný súbor musí existovať.');
        }
        if (!file_exists($php)) {
            FormValidator::getInstance()->putError('templatePhp', 'Zdrojový súbor šablóny musí existovať.');
        }
        if (!file_exists($css)) {
            $this->getLayout()->putNotificaion(new Notification('Súbor CSS neexestuje!', Notification::WARNING));
        }
        if (ContentTemplate::isTemplateNameInUse($nazov, $id)) {
            FormValidator::getInstance()->putError('templateName', 'Názov šablóny sa už používa.');
        }
        if (FormValidator::getInstance()->errorsCount()) {
            Terminal::getInstance()->putMessage('Form validation error!', Terminal::CONSOLE_WARNING);
            return;
        }
        $result = ContentTemplate::update();
        if ($result) {
            $this->getLayout()->putNotificaion(new Notification('Šablóna bola uložená.', Notification::SUCCESS));
        } else {
            $this->getLayout()->putNotificaion(new Notification('Šablónu sa nepodarilo uložiť.', Notification::ERROR));
        }

        $this->redirect(' ?content=editTemplate&id=' . $id);
    }

    protected function viewBlocks() {
        return $this->viewList('block');
    }

}
