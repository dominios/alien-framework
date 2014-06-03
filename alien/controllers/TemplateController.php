<?php

namespace Alien\Controllers;

use Alien\Application;
use Alien\View;
use Alien\Response;
use Alien\Notification;
use Alien\Models\Content\Template;
use Alien\Forms\Content\TemplateForm;
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

        $view = new View('display/content/temlateForm.php');

        $templateDao = $this->getServiceManager()->getDao('TemplateDao');
        $template = $templateDao->find($_GET['id']);

        //$template = new Template((int) $_GET['id']);
//        $template->fetchViews();

        $form = TemplateForm::factory($template);

        $view->template = $template;
        $view->form = $form;

        if ($form->isPostSubmit()) {
            if ($form->validate()) {
                if (Template::exists($_POST['templateId'])) {
                    $template = new Template($_POST['templateId']);
                    $new = false;
                } else {
                    $initialValus = array(
                        'folderId' => 1,
                        'templateName' => 'Nová šablóna',
                        'templateSrc' => ''
                    );
                    $template = Template::create($initialValus);
                    $new = true;
                }
                $template->setName($_POST['templateName']);
                $template->setDescription($_POST['templateDescription']);
                $template->setSrc($_POST['templateSrc']);
                if ($template->update()) {
                    if ($new) {
                        Notification::success('Šablóna bola vytvorená.');
                    } else {
                        Notification::success('Zmeny boli uložené.');
                    }
                    $this->redirect(BaseController::actionURL('template', 'edit', array('id' => $template->getId())));
                } else {
                    Notification::error('Zmeny sa nepodarilo uložiť.');
                }
            } else {
                Notification::error('Zmeny sa nepodarilo uložiť.');
            }
        }

        $viewFloatPanel = new View('display/content/partial/toolBox.php');
        $viewFloatPanel->type = 'template';

        return new Response(array(
                'Title' => 'Úprava šablóny: ' . $template->getName(),
                'ContentMain' => $view->renderToString(),
                'FloatPanel' => $viewFloatPanel->renderToString()
            )
        );
    }

    protected function viewBlocks() {
        return $this->viewList('block');
    }

}
