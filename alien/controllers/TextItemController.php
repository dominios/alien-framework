<?php

namespace Alien\Controllers;

use Alien\Application;
use Alien\Forms\Content\PageForm;
use Alien\Forms\Content\TextItemForm;
use Alien\Models\Content\Item;
use Alien\Models\Content\TextItem;
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

class TextItemController extends ContentController {

    protected function viewAll() {
        return $this->viewList('text');
    }

    protected function edit() {

        if (!preg_match('/^[0-9]*$/', $_GET['id'])) {
            Notification::error('Neplatný identifikátor objektu!');
            return '';
        }

        $item = Item::factory($_GET['id']);
        $form = TextItemForm::factory($item);

        $view = new View('display/content/TextItemForm.php');
        $view->form = $form;

        if ($form->isPostSubmit()) {
            if ($form->validate()) {
                if(Item::exists($_POST['itemId'])){
                    $item = Item::factory($_POST['itemId']);
                    $item->setName($_POST['itemName']);
                    $item->setContent($_POST['itemContent']);
                    if($item->update()){
                        Notification::success('Objekt bol uložený.');
                    } else {
                        Notification::error('Objekt sa nepodarilo uložiť.');
                    }
                    $this->refresh();
                }
            }
        }

        return new Response(array(
                'Title' => 'Úprava objektu: ' . $item->getName(),
                'ContentMain' => $view->renderToString()
            )
        );
    }

}