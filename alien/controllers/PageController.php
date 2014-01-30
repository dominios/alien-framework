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

class PageController extends ContentController {

    protected function homepage() {
        
    }

    protected function viewPages() {
        return $this->viewList('page');
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

}
