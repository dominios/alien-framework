<?php

namespace Alien\Controllers;

use Alien\Alien;
use Alien\Forms\Content\WidgetForm;
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
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('page', 'homepage'), 'img' => 'home', 'text' => 'Domovská stránka');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('page', 'viewPages'), 'img' => 'page', 'text' => 'Stránky');
//        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'browser', array('folder' => 0)), 'img' => 'folder', 'text' => 'ROOT');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('template', 'viewAll'), 'img' => 'template', 'text' => 'Šablóny', 'regex' => 'template');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('template', 'viewBlocks'), 'img' => 'puzzle', 'text' => 'Boxy šablón', 'regex' => 'block');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewBoxes'), 'img' => 'box', 'text' => 'Skupiny objektov');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewTexts'), 'img' => 'document', 'text' => 'Texty');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewGalleries'), 'img' => 'gallery', 'text' => 'Galérie');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewNews'), 'img' => 'magazine', 'text' => 'Novinky');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewDocuments'), 'img' => 'book-stack', 'text' => 'Dokumenty');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'viewMenus'), 'img' => 'list', 'text' => 'Menu');
        $menuItems[] = Array('permissions' => null, 'url' => BaseController::actionURL('content', 'sitemap'), 'img' => 'sitemap', 'text' => 'Mapa webu');

        return new Response(Response::OK, Array(
            'ContentLeft' => $menuItems,
            'LeftTitle' => 'Obsah webu',
            'MainMenu' => $data['MainMenu']
        ), __CLASS__ . '::' . __FUNCTION__);
    }

    protected function viewList($type) {

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
            default:
                $items = array();
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

    protected function browser() {
        return;
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

    protected function editWidget() {

        if (!preg_match('/^[0-9]*$/', $_GET['id'])) {
            Notification::error('Neplatný identifikátor widgetu.');
            $this->redirect(BaseController::actionURL('content', 'browser'));
        }

        $widget = Widget::getSpecificWidget($_GET['id']);
        $form = WidgetForm::create($widget);

        if ($form->isPostSubmit()) {
            if ($form->validate()) {
                $widget->setScript((string) $form->getElement('widgetTemplate')->getValue());
                $widget->setVisible((bool) $form->getElement('widgetVisibility')->isChecked());
                $widget->handleCustomFormElements($form);
                if ($widget->update()) {
                    Notification::success('Zmeny boli uložené.');
                    $this->redirect(BaseController::actionURL('content', 'editWidget', array('id' => $widget->getId())));
                }
            }
            Notification::error('Zmeny sa nepodarilo uložiť.');
        }

        $view = new View('display/content/widgetForm.php');
        $view->form = $form;
        $view->widget = $widget;

        $customFormPart = new View('display/content/partial/widgetCustomForm.php');
        $customFormPart->formElements = $widget->getCustomFormElements();
        $view->customPart = $customFormPart->renderToString();

        return new Response(Response::OK, Array(
            'Title' => 'Úprava widgetu: ',
            'ContentMain' => $view->renderToString()
        ), __CLASS__ . '::' . __FUNCTION__);
    }

    protected function dropWidget() {
        if (!preg_match('/^[0-9]*$/', $_GET['id']) || !Widget::exists($_GET['id'])) {
            Notification::ERROR('Neplatný identifikátor widgetu.');
            return;
        } else {
            $widget = Widget::getSpecificWidget($_GET['id']);
            $widget->delete();
            $redirectAction = BaseController::actionURL(BaseController::getControllerFromURL($_SERVER['HTTP_REFERER']), BaseController::getActionFromURL($_SERVER['HTTP_REFERER'], true));
            $this->redirect($redirectAction);

        }
    }
}
