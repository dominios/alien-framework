<?php

class ContentController extends AlienController {        
    
    protected function init_action() {
        parent::init_action();
        $this->meta_title = 'Prieskumník';        
        $this->content_left = $this->left();
    }

    private function left(){
        $ret = '';
        $ret .= '<h3>ROOT</h3>';

        return $ret;
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

        $view = new AlienView('display/content/browser.php');
        $view->DisplayLayout = 'ROW';
        $view->Folder = $folder;
        $view->Folders= $folder->getChilds(true);
        $view->Items = $folder->fetchFiles();
        
        //$_SESSION['SDATA'] = serialize($view);
        return $view->getContent();
        
    }

    protected function editTemplate(){
        if(!preg_match('/^[0-9]*$/', $_GET['id'])){
            new Notification('Neplatný identifikátor šablóny.', 'error');
            return 'ERROR';
        }

        $template = new ContentTemplate((int)$_GET['id']);

        $this->meta_title = 'Úprava šablóny: '.$template->getName();

        $view = new AlienView('display/content/temlateForm.php');
        $view->ReturnAction = '?content=browser&folder='.$_SESSION['folder'];
        $view->Template = $template;

        return $view->getContent();
    }

    protected function templateFormSubmit(){
        $errorOutput = Array();
        $id = (int)$_POST['templateId'];
        $nazov = $_POST['templateName'];
        $php = $_POST['templatePhp'];
        $ini = $_POST['templateIni'];
        if(!strlen($nazov)){
            $errorOutput[] = Array('inputName'=>'templateName', 'errorMsg'=>'Názov šablóny nemôže zostať prázdny.');
        }
        if(!file_exists($ini)){
            $errorOutput[] = Array('inputName'=>'templateIni', 'errorMsg'=>'Konfiguračný súbor musí existovat.');
        }
        if(!file_exists($php)){
            $errorOutput[] = Array('inputName'=>'templatePhp', 'errorMsg'=>'Zdrojový súbor šablóny musí existovať.');
        }
        if(ContentTemplate::isTemplateNameInUse($nazov, $id)){
            $errorOutput[] = Array('inputName'=>'templateName', 'errorMsg'=>'Zadaný názov šablóny sa už používa.');
        }
        if(sizeof($errorOutput)){
            AlienConsole::getInstance()->putMessage('Form validation error!', AlienConsole::CONSOLE_WARNING);
            $_SESSION['formErrorOutput'] = json_encode($errorOutput);
            return;
        }
        ContentTemplate::update();
        header('Location: ?content=editTemplate&id='.$id, false, 301);
        ob_end_clean();
        exit;
    }
}
?>
