<?php
class ContentFolder implements FileItem {    
    
    const ICON = 'folder.png';
    
    private $id;
    private $name;
    private $parent;
    private $childs=Array();
//    private $permissions;
    private $items=Array();
    
    public function __construct($id, $row=null){

        if($row!=null){
            $this->id=$row['id_f'];
            $this->name=$row['name'];
            $this->permissions=null;
            $this->parent=$row['parent'];
            return;
        } elseif($id===0){
            $this->id=0;
            $this->name='ROOT';
            $this->permissions=null;
            $this->parent=0;
            return;
        } else {
            $DBH=Alien::getDatabaseHandler();
            $STH=$DBH->prepare('SELECT * FROM '.Alien::getParameter('db_prefix').'_content_folders WHERE id_f=:id LIMIT 1');
            $STH->bindValue(':id',$id,PDO::PARAM_INT);
            $STH->execute();
            $result=$STH->fetch();
            $this->id=$result['id_f'];
            $this->name=$result['name'];
            $this->permissions=null;
            $this->parent=$result['parent'];
            return;
        }
    }

    public function actionGoTo(){
        return '?content=browser&folder='.$this->id;
    }

    public function actionEdit(){
        return '?content=editFolder&id='.$this->id;
    }

    public function actionDrop(){
        return '?content=dropFolder&id='.$this->id;
    }

    public static function exists($id){
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT 1 FROM '.Alien::getDBPrefix().'_content_folders WHERE id_f=:i LIMIT 1;');
        $STH->bindValue(':i', (int)$id, PDO::PARAM_INT);
        $STH->execute();
        if($STH->rowCount()){
            return true;
        } else {
            return false;
        }
    }

    public function getIcon(){
        return self::ICON;
    }

    public function getName(){
        return $this->name;
    }
    
    public function getId(){
        return $this->id;
    }  
    
    /* TODO: pozriet ci to je dobre... */
    public function drop(){        
        if($this->isEmpty()){        
            $DBH=Alien::getDatabaseHandler();
            $STH=$DBH->prepare('DELETE FROM '.Alien::getParameter('db_prefix').'_content_folders WHERE id_f=:i');
            $STH->bindValue(':i', $this->id, PDO::PARAM_INT);
            if($STH->execute()){
                new Notification('Zložka bola odstránená.', 'success');
            } else {
                new Notification('Zložku sa nepodarilo odstrániť.','error');
            }
        } else {
            new Notification('Obsah zložky musí byť prázdny, aby sa mohla odstrániť.','warning');
            new Notification('Zložku sa nepodarilo odstrániť.','error');
        }
        if(Alien::getParameter('allowRedirects')){
            ob_clean();
            $url='?page=content&action=browser&folder='.$this->parent;
            header('Location: '.$url, true, 301);
            ob_end_flush();
            exit;
        }
    }
    
    /* TODO: pozriet ci dobre, urcite ne... */
    public function update($new=false){
        
        $DBH=Alien::getDatabaseHandler();
        
        if($new){
            $STH=$DBH->prepare('INSERT INTO '.Alien::getParameter('db_prefix').'_content_folders (name,parent) VALUES (:n,:p)');
            $STH->bindValue(':p', (int)$_POST['parentFolder'], PDO::PARAM_INT);
        }        
        else {
            $STH=$DBH->prepare('UPDATE '.Alien::getParameter('db_prefix').'_content_folders SET name=:n WHERE id_f=:id');
            $STH->bindValue(':id', (int)$_POST['folderId'], PDO::PARAM_INT);
        }
        $STH->bindValue(':n', $_POST['folderName'], PDO::PARAM_STR);
        
        if($STH->execute()){
            $new ? new Notification('Zložka bola vytvorená.', "success") : new Notification('Zložka bola upravená.', 'success');
            $f=$DBH->lastInsertId();
        } else {
            $new ? new Notification("Zložku sa nepodarilo vytvoriť.", "error") : new Notification("Zložku sa nepodarilo upraviť.", "error");
            $f=$_POST['folderId'];
        }    
        if(Alien::getParameter('allowRedirects')){
            ob_clean();
            header('Location: ?page=content&action=browser&folder='.$f, true, 301);
            ob_end_flush();
            exit;
        }
    }
        
    /* TODO: nejako zoptimalizovat, ale hlavne nech funguje, aj tak sa s tym robi len v administracii */
    public function fetchFiles(){
        
        if(!sizeof($this->items)){
            $DBH=Alien::getDatabaseHandler();
            $STH=$DBH->prepare('SELECT * FROM '.Alien::getDBPrefix().'_content_templates WHERE id_f=:idf');
            $STH->bindValue(':idf', (int)$this->id, PDO::PARAM_INT);
            $STH->execute();
            while($row = $STH->fetch()){
                $this->items[] = new ContentTemplate(null, $row);
            }

            $STH=$DBH->prepare('SELECT * FROM '.Alien::getDBPrefix().'_content_pages WHERE id_f=:idf');
            $STH->bindValue(':idf', (int)$this->id, PDO::PARAM_INT);
            $STH->execute();
            while($row = $STH->fetch()){
                $this->items[] = new ContentPage(null, $row);
            }

            $STH=$DBH->prepare('SELECT * FROM '.Alien::getDBPrefix().'_content_items WHERE id_f=:idf');
            $STH->bindValue(':idf', (int)$this->id, PDO::PARAM_INT);
            $STH->execute();
            while($row=$STH->fetch()){
                if(($row['id_type']) == 1 || $row['id_type'] == 11) continue;
//                $item=ContentItem::getSpecificItem($row['id_i']);
                $item=ContentItem::getSpecificItem(null, $row);
                if($item instanceof CodeItem || $item instanceof VariableItem || $item instanceof GalleryListItem || $item instanceof GalleryVariableItem) continue;
                else $this->items[]=$item;
            }
        }
        return $this->items;
    }

    public function getChilds($fetched = false){
        if(!sizeof($this->childs)){
            $folders=Array();
            $DBH=Alien::getDatabaseHandler();
            $STH=$DBH->prepare('SELECT * FROM '.Alien::getDBPrefix().'_content_folders WHERE parent=:p');
            $STH->bindValue(':p',$this->id);
            $STH->execute();
            while($row = $STH->fetch()){
                $f=new self(null, $row);
                $f->getChilds();
                $folders[] = $f;
            }
            $this->childs=$folders;
        }
        if($fetched){
            return $this->childs;
        } else {
            $a = Array();
            foreach ($this->childs as $c){
                $a[] = $c->getId();
            }
            return $a;
        }
    }
    
    /**
     * ziska cestu z rootu
     * @param boolean $includeThis ci ma zahrnut aj tuto
     * @return \ContentFolder 
     */
    public function getPathFromRoot($includeThis = true, $fetch = false){
        $folders=Array();
        if($includeThis && $this->id!=0){
            $folders[] = $fetch ? $this : $this->id;
        }
        $f=$this->getParent();
        while(true){
            if($f->getId()>=1){
                $folders[] = $fetch ? $f : $f->id;
                $f=$f->getParent();
            } else {
                break;
            }
        }
        $folders[] = $fetch ? new ContentFolder(0) : 0;
        asort($folders);        
        return $folders;
    }
        
    public function getParent($fetch = false){
        if($this->parent==0){
            return $fetch ? new ContentFolder(0) : 0;
        }
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare('SELECT * FROM '.Alien::getParameter('db_prefix').'_content_folders WHERE id_f=:p && id_f>=1');
        $STH->bindValue(':p',$this->parent,PDO::PARAM_INT);
        $STH->execute();        
        $result=$STH->fetch();
        return $fetch ? new ContentFolder(null, $result) : $result['id_f'];
    }
    
    
    
    
    
    
    
    
    
    
    /* hentie presunut ako views
    
    public function renderTree(){
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare('SELECT * FROM '.Alien::getParameter('db_prefix').'_content_folders WHERE parent=:p');
        $STH->bindValue(':p',$this->id,PDO::PARAM_INT);
        $STH->execute();
        while($row=$STH->fetch()){
            $folder=new ContentFolder(null, $row);
            echo ('<a class="folder" href="?page=content&amp;task=browser&amp;folder='.$folder->getId().'"><img src="images/icons/folder.png"> '.$folder->getName().'</a>');
            $folder->renderTree();
        }
    }
    
    public static function renderForm($folderId=null){
        
        echo ('<form name="folderForm" method="POST"><fieldset><legend>Adresár</legend>');
        
        switch ($folderId){
            case null: $new=true; $folder=null; break;
            default: $new=false; $folder=new self($folderId); break;
        }
        
        if($new){
            Alien::setHeading('Nový adresár');
            echo '<input type="hidden" name="action" value="folderSubmit">';
            echo '<input type="hidden" name="parentFolder" value="'.(int)$_GET['parent'].'">';
        } else {
            Alien::setHeading('Úprava adresára: '.$folder->name);
            echo '<input type="hidden" name="action" value="folderSubmit">';
            echo '<input type="hidden" name="folderId" value="'.$folder->id.'">';
        }
              
        echo ('<table class="noborders">');
        echo ('<tr><td><img src="images/icons/folder.png"> Názov zložky: </td><td><input type="text" name="folderName" value="'.($new ? '' : $folder->getName()).'"></td></tr>');
        echo ('<tr><td colspan="2">');
            echo ('<div class="button negative" onClick="javascript: window.location=\'?page=content&amp;action=browser&amp;folder='.$_SESSION['folder'].'\';"><img src="images/icons/cancel.png"> Zrušiť</div>');
            echo ('<div class="button positive" onClick="javascript: $(\'form[name=folderForm]\').submit();"><img src="images/icons/save.png"> Uložiť</div>');
        echo ('</td></tr>');
        echo ('</table></fieldset></form>');
    }
    
*/










    /**
     * vsetky foldre
     * @return \self 
     */
    public static function getFolderList(){
        $folders=Array();
        $folders[]=new self(0);
        $DBH=Alien::getDatabaseHandler();
        foreach($DBH->query('SELECT * FROM '.Alien::getDBPrefix().'_content_folders') as $row){
            $f=new self(null, $row);
            $f->getChilds();
            $folders[]=$f;
        }
        return $folders;
    }

 
    
    public function getPermissions(){
        return $this->permissions;
    }

    /**
     * ci je prazdna
     * @return boolean 
     */
    public function isEmpty(){
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare('SELECT 1 FROM '.Alien::getParameter('db_prefix').'_content_folders WHERE parent=:id LIMIT 1');
        $STH->bindValue(':id',$this->id);
        $STH->execute();
        if($STH->rowCount()) return false;
        $STH=$DBH->prepare('SELECT 1 FROM '.Alien::getParameter('db_prefix').'_content_items WHERE id_f=:id LIMIT 1');
        $STH->bindValue(':id',$this->id);
        $STH->execute();
        if($STH->rowCount()) return false;
        $STH=$DBH->prepare('SELECT 1 FROM '.Alien::getParameter('db_prefix').'_content_pages WHERE id_f=:id LIMIT 1');
        $STH->bindValue(':id',$this->id);
        $STH->execute();
        if($STH->rowCount()) return false;
        $STH=$DBH->prepare('SELECT 1 FROM '.Alien::getParameter('db_prefix').'_content_templates WHERE id_f=:id LIMIT 1');
        $STH->bindValue(':id',$this->id);
        $STH->execute();
        if($STH->rowCount()) return false;        
        return true;
    }

    /**
     * pri foldri blbost, ci je pouzita
     * @return boolean 
     */
    public function isUsed(){
        return !$this->isEmpty();
    }

    /** TODO: prerobit ako view
     * vykresli ovladaci panel 
     */ /*
    public function renderControlPanel(){
        echo ('<div style="float: right; display: inline-block; position: relative;">');
        $buttons=Array();
        if(Authorization::permissionTest(null, array())){            
            $url='';
            // todo opravnen
//            $buttons[]=('<a href="'.$url.'"><img class="button" src="images/icons/shield.png" alt="Oprávnenia" title="Oprávnenia"></a>');
        }
        if(Authorization::permissionTest(null, array()) && $this->isEmpty()){
            $url='?page=content&action=dropFolder&folder='.$this->id;
            $buttons[]=('<a href="'.$url.'"><img class="button negative" src="images/icons/cross.png" alt="Vymazať" title="Vymazať"></a>');
        }
        echo implode(' ',$buttons);
        echo ('</div>');
        echo '<br/>';
    }*/

}

?>
