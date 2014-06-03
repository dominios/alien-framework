<?php

namespace Alien\Controllers;

use Alien\Application;
use Alien\Forms\Content\PageForm;
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

    protected function viewAll() {
        return $this->viewList('page');
    }

    protected function edit() {

        if (!preg_match('/^[0-9]*$/', $_GET['id'])) {
            Notification::error('Neplatný identifikátor stránky!');
            return '';
        }

        $view = new View('display/content/pageForm.php');
        $page = new Page((int) $_GET['id']);
        $form = PageForm::factory($page);

        $view->page = $page;
        $view->form = $form;

        if ($form->isPostSubmit()) {
            if ($form->validate()) {
                if (Page::exists($_POST['pageId'])) {
                    $page = new Page($_POST['pageId']);
                    $new = false;
                } else {
                    $initialValus = array(
                        'folderId' => 1,
                        'pageName' => 'Nová stránka',
                        'pageTemplate' => $_POST['pageTemplate'],
                        'pageSeolink' => $_POST['pageSeolink']
                    );
                    $page = Page::create($initialValus);
                    $new = true;
                }
                $page->setName($_POST['pageName'])
                     ->setDescription($_POST['pageDescription'])
                     ->setSeolink($_POST['pageSeolink'])
                     ->setTemplate(new Template($_POST['pageTemplate']));
                if ($page->update()) {
                    if ($new) {
                        Notification::success('Strákna bola vytvorená.');
                    } else {
                        Notification::success('Zmeny boli uložené.');
                    }
                    $this->redirect(BaseController::actionURL('page', 'edit', array('id' => $page->getId())));
                } else {
                    Notification::error('Zmeny sa nepodarilo uložiť.');
                }
            } else {
                Notification::error('Zmeny sa nepodarilo uložiť.');
            }
        }

        $viewFloatPanel = new View('display/content/partial/toolBox.php');
        $viewFloatPanel->type = 'page';

        return new Response(array(
                'Title' => 'Úprava stránky: ' . $page->getName(),
                'ContentMain' => $view->renderToString(),
                'FloatPanel' => $viewFloatPanel->renderToString()
            )
        );
    }

}
