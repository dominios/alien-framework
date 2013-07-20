<?php

class ContentTemplate implements FileItem {

    const ICON = 'template.png';
    
    private $id;
    private $name;
    private $description;
    private $html_url;
    private $css_url;
    private $config_url;
    private $blocks;
    
    public function __construct($id, $row=null){
        if($row===null){
            $DBH = Alien::getDatabaseHandler();
            $STH=$DBH->prepare("SELECT * FROM ".Alien::getDBPrefix()."_content_templates WHERE id_t=:id LIMIT 1");
            $STH->bindValue(':id',$id);
            $STH->execute();
            $row=$STH->fetch();
        }
        $this->id=$row['id_t'];
        $this->name=$row['name'];
        $this->html_url=$row['html_url'];
        $this->css_url=$row['css_url'];
        $this->config_url=$row['config_url'];
        $this->description=$row['description'];
        $this->blocks=parse_ini_file($row['config_url']);
    }

/* ******** STATIC METHODS ********************************************************************* */
    
    public static function templateExists($id){
        return AlienContent::getInstance()->templateExists($id);
    }
    
    public static function getTempatesList(){
        $DBH = Alien::getDatabaseHandler();
        $arr=array();
        $STH=$DBH->prepare("SELECT id_t FROM ".Alien::getParameter('db_prefix')."_content_templates");
        $STH->execute();
        while($item=$STH->fetch()){
            $arr[]=new ContentTemplate($item['id_t']);
        }
        return $arr;
    }

    // DOROBIT PERMISSION TEST
    public static function update(){        
        
        // najprv kontrola spravnosti udajov
        
        $continue = true;
        if(empty($_POST['templateName']) || $_POST['templateName']==''){
            new Notification("Názov šablóny nesmie byť prázdny reťazec.", "warning");
            $continue=false;
        }
        if(empty($_POST['templateDesc']) || $_POST['templateDesc']==''){
            new Notification("Je odporúčané vyplniť pomocný popis.","note");
        }
        if(empty($_POST['templateHtml']) || $_POST['templateHtml']==''){
            new Notification("Šablóna musí mať pridelený svoj zdrojový súbor s HTML kódom.","warning");
            $continue=false;
        }
        if(@strtolower(end(explode('.',$_POST['templateHtml'])))!='php' && $_POST['templateHtml']!=''){
            new Notification("Zdrojovú súbor šablóny musí mať koncovku .php.","note");
            $continue=false;
        }            
        if(empty($_POST['templateCss']) || $_POST['templateCss']==''){
            new Notification("Odporúča sa priradiť šablóne vlastný CSS štýl.","warning");
        }
        if(@strtolower(end(explode('.',$_POST['templateCss'])))!='css' && $_POST['templateCss']!=''){
            new Notification("Vlastný CSS štýl šablóny musí mať koncovku .css.","note");
            $continue=false;
        }  
        if(empty($_POST['templateConfig']) || $_POST['templateConfig']==''){
            new Notification("Šablóna musí mať prideleý konfiguračný súbor.","warning");
            $continue=false;
        }
        if(@strtolower(end(explode('.',$_POST['templateConfig'])))!='ini' && $_POST['templateConfig']!=''){
            new Notification("Konfiguračný súbor musí mať koncovku .ini.","note");
            $continue=false;
        }        
        if(!$continue){
            new Notification('Šablónu nieje možné uložiť.', 'error');
            return;
        }
        
        $DBH=Alien::getDatabaseHandler();
        
        if(@$_POST['templateId']<0){ // nova sablona, zapordne IDcko
            $new = true;
            $STH=$DBH->prepare('INSERT INTO '.Alien::getParameter('db_prefix').'_content_templates (id_f, name, html_url, css_url, config_url, description) VALUES (:f, :n, :html, :css, :ini, :d)');
        } else {
            $new = false;
            $STH=$DBH->prepare('UPDATE '.Alien::getParameter('db_prefix').'_content_templates SET id_f=:f, name=:n, html_url=:html, css_url=:css, config_url=:ini, description=:d WHERE id_t=:id');
            $STH->bindValue(':id', $_POST['templateId'], PDO::PARAM_INT);
        }
        
        $STH->bindValue(':f', $_SESSION['folder']);
        $STH->bindValue(':n', $_POST['templateName'], PDO::PARAM_STR);
        $STH->bindValue(':html', $_POST['templateHtml'], PDO::PARAM_STR);
        $STH->bindValue(':css', $_POST['templateCss'], PDO::PARAM_STR);
        $STH->bindValue(':ini', $_POST['templateConfig'], PDO::PARAM_STR);
        $STH->bindValue(':d', $_POST['templateDesc'], PDO::PARAM_STR);
        
        if($STH->execute()){
            $new ? new Notification("Nová šablóna bola úspešne vytvorená.","success") : new Notification('Šablóna bola uložená.', 'success');
        } else {
            $new ? new Notification("Šablónu sa nepodarilo vytvoriť.", "error") : new Notification('Šablónu sa nepodarilo uložiť.', 'error');
        }
        if(Alien::getParameter('allowRedirects')){
            ob_clean();
            $id = $new ? $DBH->lastInsertId() : $_POST['templateId'];
            $url='?page=content&action=editTemplate&id='.$id;
            header('Location: '.$url, true, 301);
            ob_end_flush();
            exit;
        }
        
    }
    
    public static function drop($id){
        $template=new ContentTemplate($id);
        if($template->isUsed()){
            new Notification('Nie je možné vymazať šablónu, ktorá sa používa.', 'warning');
            new Notification('Nastavte všetky stránke tak, aby nepoužívali túto šablónu.', 'notice');
            new Notification('Nepodarilo sa odstrániť šablónu.', 'error');
        } else {
            $DBH=Alien::getDatabaseHandler();
            $STH=$DBH->prepare('DELETE FROM '.Alien::getParameter('db_prefix').'_content_templates WHERE id_t=:i');
            $STH->bindValue(':i',$_GET['id']);
            if($STH->execute()){
                new Notification('Šablóna bola zmazaná.', 'success');
            } else {
                new Notification('Šablónu sa nepodarilo zmazať.', 'error');
            }
        }
        if(Alien::getParameter('allowRedirects')){
            ob_clean();
            $url='?page=content&action=browser&folder='.$_SESSION['folder'];
            header('Location: '.$url, true, 301);
            ob_end_flush();
            exit;
        }
    }

    public static function renderTemplatesList(){
//        Authorization::permissionTest('?page=home',array(4));
        $cp=('<div class="controlPanel">
            <a href="?page=content&amp;task=templates&amp;do=new"><img src="images/icons/layout_add.png" title="refresh" alt="refresh"> Nová šablóna</a>
            <a href="?page=content&amp;task=templates"><img src="images/icons/refresh.png" title="refresh" alt="refresh"> Refresh</a> 
            <a href="#" onClick="javascript: viewMan(\'content.template.man\');"><img src="images/icons/help.png" title="manuál"> Manuál</a> 
        </div>');

        Alien::setHeading(obsahSablony.$cp);
              
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare("SELECT id_t FROM ".Alien::getParameter('db_prefix')."_content_templates");
        $STH->setFetchMode(5);
        $STH->execute();
        while($obj=$STH->fetch()){
            $template=new contentTemplate($obj->id_t);
            $template->renderInFolder();
        }   
    }

/* ******** SPECIFIC  METHODS ****************************************************************** */

    public function actionEdit(){
        return '?content=editTemplate&id='.$this->id;
    }

    public function actionGoTo(){
        return $this->actionEdit();
    }

    public function actionDrop(){
        return '?content=dropTemplate&id='.$this->id;
    }

    public function getId(){
        return $this->id;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getHtmlUrl(){
        return $this->html_url;
    }
    
    public function getCssUrl(){
        return $this->css_url;
    }
    
    public function getConfigUrl(){
        return $this->config_url;
    }
    
    /**
     * ziska pole blokov podla configu
     * @return Array 
     */
    public function getTemplateBlocks(){
        return $this->blocks;
    }
    
    public function getDescription(){
        return $this->description;
    }
        
    public function isUsed(){
        global $DBH;
        $STH=$DBH->prepare('SELECT 1 FROM '.Alien::getParameter('db_prefix').'_content_pages WHERE id_t=:id');
        $STH->bindValue(':id', $this->id, PDO::PARAM_INT);
        $STH->execute();
        if($STH->rowCount()){
            return true;
        } else {
            return false;
        }
    }    
    
    public function renderControlPanel(){
        echo('<div style="float: right; display: inline-block; position: relative;">');
        $editAction = '?page=content&amp;action=editTemplate&amp;id='.$this->id;
        $deleteAction = 'javascript: if(confirm(\'Naozaj odstrániť túto šablónu?\')) window.location=\'?page=content&amp;action=dropTemplate&amp;id='.$this->id.'\'';
        if(Authorization::permissionTest(null, Array('TEMPLATE_EDIT', 'CONTENT_EDIT'))) echo ('<a href="'.$editAction.'"><img class="button" src="images/icons/layout_edit.png" title="Edit template" alt="Edit"></a>');
        if(Authorization::permissionTest(null, Array('TEMPLATE_EDIT', 'CONTENT_EDIT'))) echo ($this->isUsed() ? '' : '<a href="#" onClick="'.$deleteAction.'"><img class="button" src="images/icons/layout_delete.png" title="Delete template" alt="delete"></a>');
        echo ('</div><br style="clear: right;">');
    }
    
    public function getFolderRenderOptions() {
        $options=Array();
        $options['image']='images/icons/template.png';
        $options['name']=$this->name;
        return $options;
    }

    public function renderInFolder() {
        $options=$this->getFolderRenderOptions();
        echo ('<div class="item"><img src="'.$options['image'].'"> <b>'.$options['name'].'</b>');
            $this->renderControlPanel();
            echo ('&nbsp;&nbsp;ID: '.$this->id.'&nbsp;|&nbsp;'. $this->description.' &nbsp;|&nbsp;'.obsahZdroj.': '.$this->html_url);
        echo ('</div>');
    }
    
    public function sortItems($items){        
        $DBH=Alien::getDatabaseHandler();        
        $STH=$DBH->prepare("UPDATE ".Alien::getDBPrefix()."_content_views SET position=:p WHERE id_v=:id");          
        foreach($items as $string){
            $i=1;
            $data=explode(',', $string);
            foreach($data as $item){
                $STH->bindValue(':id',$item,PDO::PARAM_INT);
                $STH->bindValue(':p',$i++,PDO::PARAM_INT);
                $STH->execute();
            }
        }        
        return;
    }

    public function getIcon(){
        return self::ICON;
    }
}

?>
