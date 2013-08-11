<?php

class ContentController extends AlienController {        
    
    protected function init_action() {

        $parentResponse = parent::init_action();
        if($parentResponse instanceof ActionResponse){
            $data = $parentResponse->getData();
        }

        $left = '';
        $left .= '<h3>ROOT</h3>';

        return new ActionResponse(ActionResponse::RESPONSE_OK, Array(
            'ContentLeft' => $left,
            'MainMenu' => $data['MainMenu']
        ), __CLASS__.'::'.__FUNCTION__);
    }

    protected function browser(){

        $f = isset($_GET['folder']) ? $_GET['folder'] : $_SESSION['folder'];
        $folder = null;
        if(empty($f) || $f===null || $f===''){
            $folder = new ContentFolder(0);
        } else {
            $folder = new ContentFolder($f);
            $_SESSION['folder'] = $f;
        }

        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT * FROM '.Alien::getDBPrefix().'_content_folders WHERE id_f=:i');
        $STH->bindValue(':i', $f, PDO::PARAM_INT);
        $STH->execute();
        if($STH->rowCount() && $folder !== null){
            $folder = new ContentFolder(null, $STH->fetch());            
        }               

        $view = new AlienView('display/content/browser.php', $this);
        $view->DisplayLayout = 'ROW';
        $view->Folder = $folder;
        $view->Folders= $folder->getChilds(true);
        $view->Items = $folder->fetchFiles();
        
        //$_SESSION['SDATA'] = serialize($view);

        return new ActionResponse(ActionResponse::RESPONSE_OK, Array(
            'Title' => 'Prieskumník',
            'ContentMain' => $view->getContent()
        ), __CLASS__.'::'.__FUNCTION__);
    }

    protected function editTemplate(){
        if(!preg_match('/^[0-9]*$/', $_GET['id'])){
            new Notification('Neplatný identifikátor šablóny.', Notification::ERROR);
            return;
        }

        $template = new ContentTemplate((int)$_GET['id']);

        $view = new AlienView('display/content/temlateForm.php', $this);
        $view->ReturnAction = '?content=browser&folder='.$_SESSION['folder'];
        $view->Template = $template;


        return new ActionResponse(ActionResponse::RESPONSE_OK, Array(
            'Title' => 'Úprava šablóny: '.$template->getName(),
            'ContentMain' => $view->getContent()
        ), __CLASS__.'::'.__FUNCTION__);
    }

    protected function templateFormSubmit(){
        $id = $_POST['templateId'];
        $nazov = $_POST['templateName'];
        $php = $_POST['templatePhp'];
        $ini = $_POST['templateIni'];
        $css = $_POST['templateCss'];
        if(!preg_match('/^[0-9]*$/', $id)){
            return;
        }
        if(!strlen($nazov)){
            FormValidator::getInstance()->putError('templateName', 'Názov šablóny nemôže ostať prázdny.');
        }
        if(!file_exists($ini)){
            FormValidator::getInstance()->putError('templateIni', 'Konfiguračný súbor musí existovať.');
        }
        if(!file_exists($php)){
            FormValidator::getInstance()->putError('templatePhp', 'Zdrojový súbor šablóny musí existovať.');
        }
        if(!file_exists($css)){
            $this->getLayout()->putNotificaion(new Notification('Súbor CSS neexestuje!', Notification::WARNING));
        }
        if(ContentTemplate::isTemplateNameInUse($nazov, $id)){
            FormValidator::getInstance()->putError('templateName', 'Názov šablóny sa už používa.');
        }
        if(FormValidator::getInstance()->errorsCount()){
            AlienConsole::getInstance()->putMessage('Form validation error!', AlienConsole::CONSOLE_WARNING);
            return;
        }
        $result = ContentTemplate::update();
        if($result){
            $this->getLayout()->putNotificaion(new Notification('Šablóna bola uložená.', Notification::SUCCESS));
        } else {
            $this->getLayout()->putNotificaion(new Notification('Šablónu sa nepodarilo uložiť.', Notification::ERROR));
        }

        $this->redirect(' ?content=editTemplate&id='.$id);
    }

    protected function editPage(){
        if(!preg_match('/^[0-9]*$/', $_GET['id'])){
            new Notification('Neplatný identifikátor stránky.', 'error');
            return '';
        }

        $page = new ContentPage((int)$_GET['id']);

        $this->meta_title = 'Úprava stránky: '.$page->getName();
        $view = new AlienView('display/content/pageForm.php', $this);
        $view->ReturnAction = '?content=browser&folder='.$_SESSION['folder'];
        $view->Page = $page;

        return new ActionResponse(ActionResponse::RESPONSE_OK, Array(
            'Title' => 'Úprava stránky: '.$page->getName(),
            'ContentMain' => $view->getContent()
        ), __CLASS__.'::'.__FUNCTION__);
    }

    protected function pageFormSubmit(){
        $id = $_POST['pageId'];
        $nazov = $_POST['pageName'];
        $seolink = $_POST['pageSeolink'];
        if(!preg_match('/^[0-9]*$/', $id)){
            return;
        }
        if(!strlen($nazov)){
            FormValidator::getInstance()->putError('pageName', 'Názov stránky nemôže ostať prázdny.');
        }
        if(ContentPage::isSeolinkInUse($seolink, $id)){
            FormValidator::getInstance()->putError('pageSeolink', 'Zadaný seolink sa už používa.');
        }
        if(FormValidator::getInstance()->errorsCount()){
            AlienConsole::getInstance()->putMessage('Form validation error!', AlienConsole::CONSOLE_WARNING);
            return;
        }
        $result = ContentPage::update();
        if($result){
            $this->getLayout()->putNotificaion(new Notification('Stránka bola uložená.', Notification::SUCCESS));
        } else {
            $this->getLayout()->putNotificaion(new Notification('Stránku sa nepodarilo uložiť.', Notification::ERROR));
        }

        $this->redirect('?content=editPage&id='.$id);
    }

}

