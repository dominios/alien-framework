<?php

class ContentController extends AlienController {        
    
    protected function init_action() {
        parent::init_action();
        $this->meta_title = 'Prieskumník';        
        $this->content_left = $this->left();
    }
    

    protected function browser(){
        
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT * FROM '.Alien::getDBPrefix().'_content_folders WHERE id_f=:i');
        $f = isset($_GET['folder']) ? $_GET['folder'] : $_SESSION['folder'];
        $folder = null;
        if(empty($f) || $f===null || $f===''){
            $folder = new ContentFolder(0);
        }
        $STH->bindValue(':i', $f, PDO::PARAM_INT);
        $STH->execute();
        if($STH->rowCount() && $folder !== null){
            $folder = new ContentFolder(null, $STH->fetch());            
        }               

        $view = new AlienView('display/browser.php');
        $view->DisplayLayout = 'ROW';
        $view->Folders[] = $folder;
        
        $_SESSION['SDATA'] = serialize($view);        
        return $view->getContent();
        
    }
    
    private function left(){
        $ret = '';
        $ret .= '<h3>ROOT</h3>';

        return $ret;
    }
}
?>
