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

class ContentController extends BaseController {

    protected function init_action() {

        $parentResponse = parent::init_action();
        if ($parentResponse instanceof Response) {
            $data = $parentResponse->getData();
        }

        $menuItems = Array();
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'homepage'), 'img' => 'home', 'text' => 'Domovská stránka');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'sitemap'), 'img' => 'sitemap', 'text' => 'Mapa webu');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'browser', array('folder' => 0)), 'img' => 'folder', 'text' => 'ROOT');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewTemplates'), 'img' => 'template', 'text' => 'Šablóny');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewTemplateBlocks'), 'img' => 'varx', 'text' => 'Boxy šablón');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewBoxes'), 'img' => 'box', 'text' => 'Skupiny objektov');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewPages'), 'img' => 'page', 'text' => 'Stránky');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewGalleries'), 'img' => 'gallery', 'text' => 'Galérie');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewNews'), 'img' => 'magazine', 'text' => 'Novinky');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewDocuments'), 'img' => 'book-stack', 'text' => 'Dokumenty');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewMenus'), 'img' => 'list', 'text' => 'Menu');

        return new Response(Response::OK, Array(
            'ContentLeft' => $menuItems,
            'LeftTitle' => 'Obsah webu',
            'MainMenu' => $data['MainMenu']
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    private function viewList($type) {

        switch ($type) {
            case 'template':
                $items = Template::getList(true);
                $name = 'šablón';
                break;
            case 'page':
//                $items = Page::getList(true);
                $name = 'stránok';
                break;
            case 'block':
                $items = TemplateBlock::getList(true);
                $name = 'blokov šablón';
                break;
            default: $items = array();
                break;
        }

        $newButton = Input::button(BaseController::actionURL('content', 'newBlock'), 'Pridať', 'icon-plus');

        $view = new View('display/content/viewList.php');
        $view->items = $items;
        $view->buttonNew = $newButton;


        return new Response(Response::OK, Array(
            'Title' => 'Zoznam ' . $name,
            'ContentMain' => $view->renderToString()
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    protected function viewTemplates() {
        return $this->viewList('template');
    }

    protected function viewTemplateBlocks() {
        return $this->viewList('block');
    }

    protected function viewPages() {
        return $this->viewList('page');
    }

    protected function browser() {

        $f = isset($_GET['folder']) ? $_GET['folder'] : $_SESSION['folder'];
        $folder = null;
        if (empty($f) || $f === null || $f === '') {
            $folder = new Folder(0);
        } else {
            $folder = new Folder($f);
            $_SESSION['folder'] = $f;
        }

        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT * FROM ' . Alien::getDBPrefix() . '_content_folders WHERE id_f=:i');
        $STH->bindValue(':i', $f, PDO::PARAM_INT);
        $STH->execute();
        if ($STH->rowCount() && $folder !== null) {
            $folder = new Folder(null, $STH->fetch());
        }

        $view = new View('display/content/browser.php', $this);
        $view->DisplayLayout = 'ROW';
        $view->Folder = $folder;
        $view->Folders = $folder->getChilds(true);
        $view->Items = $folder->fetchFiles();

        //$_SESSION['SDATA'] = serialize($view);

        return new Response(Response::OK, Array(
            'Title' => 'Prieskumník',
            'ContentMain' => $view->renderToString()
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    protected function editTemplate() {

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
        $view->buttonSrcChoose = $buttonSrcChoose;
        $view->buttonSrcMagnify = $buttonSrcMagnify;
        $buttonIniChoose = Input::button('javascript: templateShowFileBrowser(\'ini\');', '', 'icon-external-link');
        $buttonIniMagnify = Input::button('javascript: templateShowFilePreview($(\'input[name=templateIni]\').attr(\'value\'));', '', 'icon-magnifier');
        $view->buttonIniChoose = $buttonIniChoose;
        $view->buttonIniMagnify = $buttonIniMagnify;

        $view->returnAction = BaseController::actionURL('content', 'browser', array('folder' => $_SESSION['folder']));
        $view->template = $template;
        $view->form = $form;

        $viewFloatPanel = new View('display/content/templateFloatPanel.php');

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

    protected function editPage() {
        if (!preg_match('/^[0-9]*$/', $_GET['id'])) {
            new Notification('Neplatný identifikátor stránky.', 'error');
            return '';
        }

        $page = new Page((int) $_GET['id']);

//        $this->meta_title = 'Úprava stránky: ' . $page->getName();
        $view = new View('display/content/pageForm.php', $this);
        $view->ReturnAction = '?content=browser&folder=' . $_SESSION['folder'];
        $view->Page = $page;

        return new Response(Response::OK, Array(
            'Title' => 'Úprava stránky: ' . $page->getName(),
            'ContentMain' => $view->renderToString()
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    protected function pageFormSubmit() {
        $id = $_POST['pageId'];
        $nazov = $_POST['pageName'];
        $seolink = $_POST['pageSeolink'];
        if (!preg_match('/^[0-9]*$/', $id)) {
            return;
        }
        if (!strlen($nazov)) {
            FormValidator::getInstance()->putError('pageName', 'Názov stránky nemôže ostať prázdny.');
        }
        if (ContentPage::isSeolinkInUse($seolink, $id)) {
            FormValidator::getInstance()->putError('pageSeolink', 'Zadaný seolink sa už používa.');
        }
        if (FormValidator::getInstance()->errorsCount()) {
            Terminal::getInstance()->putMessage('Form validation error!', Terminal::CONSOLE_WARNING);
            return;
        }
        $result = ContentPage::update();
        if ($result) {
            $this->getLayout()->putNotificaion(new Notification('Stránka bola uložená.', Notification::SUCCESS));
        } else {
            $this->getLayout()->putNotificaion(new Notification('Stránku sa nepodarilo uložiť.', Notification::ERROR));
        }

        $this->redirect('?content=editPage&id=' . $id);
    }

    protected function editWidget() {
        if (!preg_match('/^[0-9]*$/', $_GET['id'])) {
            $this->getLayout()->putNotificaion(new Notification('Neplatný identifikátor widgetu.', Notification::ERROR));
            return '';
        }

        $view = new View('display/content/widgetForm.php', $this);
        $view->returnAction = BaseController::actionURL('content', 'browser');
        $view->widget = Widget::getSpecificWidget($_GET['id']);

        return new Response(Response::OK, Array(
            'Title' => 'Úprava widgetu: ',
            'ContentMain' => $view->renderToString()
                ), __CLASS__ . '::' . __FUNCTION__);
    }

}
