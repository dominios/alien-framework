<?php

namespace Alien\Models\Content;

use Alien\Alien;
use Alien\Controllers\BaseController;
use Alien\Models\Content\Template;
use \PDO;

class Page implements FileItem {

    const ICON = 'page.png';
    const BROWSEABLE = true;

    private $id;
    private $name;
    private $template;
    private $seolink;
    private $keywords;
    private $description;
    private $folder;

    public function __construct($identifier, $row = null) {

        if ($row === null) {
            $DBH = Alien::getDatabaseHandler();
            if (is_numeric($identifier)) {
                $Q = $DBH->prepare('SELECT * FROM ' . Alien::getDBPrefix() . '_content_pages WHERE id_p=:i');
                $Q->bindValue(':i', $identifier, PDO::PARAM_INT);
            } else {
                $Q = $DBH->prepare('SELECT * FROM ' . Alien::getDBPrefix() . '_content_pages WHERE seolink=:i');
                $Q->bindValue(':i', $identifier, PDO::PARAM_STR);
            }
            $Q->execute();
            if (!$Q->rowCount()) {

            }
            $row = $Q->fetch();
        }

        $this->id = $row['id_p'];
        $this->name = $row['name'];
        $this->template = $row['id_t'];
        $this->seolink = $row['seolink'];
        $this->keywords = $row['keywords'];
        $this->description = $row['description'];
        $this->folder = $row['id_f'];
    }

    public function isBrowseable() {
        return self::BROWSEABLE;
    }

    public static function drop($id) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('DELETE FROM ' . Alien::getDBPrefix() . '_content_pages WHERE id_p=:i LIMIT 1');
        $STH->bindValue(':i', $id, PDO::PARAM_INT);
        $STH->execute();
        if ($STH->rowCount()) {
            //new Notification('Stránka bola odstránená.', 'success');
            return true;
        } else {
            //new Notification('Stránku sa nepodarilo odstrániť.', 'error');
            return false;
        }
    }

    public static function update() {

        $new = $_POST['pageId'] == 0 ? true : false;

        $DBH = Alien::getDatabaseHandler();

        if ($new) {
            $Q = $DBH->prepare('INSERT INTO ' . Alien::getDBPrefix() . '_content_pages (id_t, id_f, name, seolink, description, keywords) VALUES (:t, :f, :n, :s, :d, :k);');
        } else {
            $Q = $DBH->prepare('UPDATE ' . Alien::getDBPrefix() . '_content_pages SET id_t=:t, id_f=:f, name=:n, description=:d, seolink=:s, keywords=:k WHERE id_p=:i LIMIT 1;');
            $Q->bindValue(':i', $_POST['pageId'], PDO::PARAM_INT);
        }
        $Q->bindValue(':n', $_POST['pageName'], PDO::PARAM_STR);
        $Q->bindValue(':d', $_POST['pageDescription'], PDO::PARAM_STR);
        $Q->bindValue(':t', $_POST['pageTemplate'], PDO::PARAM_INT);
        $Q->bindValue(':f', $_POST['pageFolder'], PDO::PARAM_INT);
        $Q->bindValue(':s', $_POST['pageSeolink'], PDO::PARAM_STR);
        $Q->bindValue(':k', $_POST['pageKeywords'], PDO::PARAM_STR);
        if ($Q->execute()) {
            //new Notification('Stránka bola uložená.', Notification::SUCCESS);
            return true;
        } else {
            //new Notification('Stránku sa nepodarilo uložiť.', Notification::ERROR);
            return false;
        }

        return;
        // vytvor novu
        if (@$_POST['pageId'] < 0) {
            Authorization::permissionTest('?page=home', array('page_create'));
            $continue = true;
            if ($_POST['pageTitle'] == '') {
                new Notification("Musíte zadať názov stránky.", "warning");
                $continue = false;
            }
            if ($_POST['pageSeolink'] == '') {
                new Notification("Musíste zadať seolink pre stránku.", "warning");
                $continue = false;
            }
            if ($_POST['pageTemplate'] == '') {
                new Notification("Musíte vybrať jednu z dostupných šablón.", "warning");
                $continue = false;
            }
            if (!$continue) {
                new Notification("Stránku sa nepodarilo vytvoriť.", "error");
                return;
            }
            $DBH = Alien::getDatabaseHandler();
            $STH = $DBH->prepare("INSERT INTO " . Alien::getParameter('db_prefix') . "_content_pages (id_t,id_f,name,seolink,keywords,visible) VALUES (:idt,:idf,:name,:seolink,:words,:vis)");
            $STH->bindValue(':idt', $_POST['pageTemplate'], PDO::PARAM_INT);
            $STH->bindValue(':name', $_POST['pageTitle'], PDO::PARAM_STR);
            $STH->bindValue(':seolink', $_POST['pageSeolink'], PDO::PARAM_STR);
            $STH->bindValue(':words', $_POST['pageKeywords'], PDO::PARAM_STR);
            $STH->bindValue(':idf', $_POST['pageFolder'], PDO::PARAM_INT);
            if (Alien::getParameter("newPageVisible") == 1) {
                $STH->bindValue(':vis', 1, PDO::PARAM_INT);
            } else {
                $STH->bindValue(':vis', 0, PDO::PARAM_INT);
            }
            if ($STH->execute()) {
                new Notification("Stránka bola úspešne vytvorená.", "success");
            } else {
                new Notification("Nieje možné vytvoriť stránku s už existujúcim seolinkom.", "warning");
                return;
            }
            if (Alien::getParameter('allowRedirects')) {
                header("Location: ?page=content&action=browser&folder=" . $_SESSION['folder'], true, 301);
                ob_end_flush();
                exit;
            }
            // update existujucej
        } else {
            if (empty($_POST['pageId']) || !is_numeric($_POST['pageId']))
                return;
            Authorization::permissionTest('?page=home', array(9));
            global $DBH;
            $STH = $DBH->prepare("UPDATE " . Alien::getParameter('db_prefix') . "_content_pages SET name=:n WHERE id_p=:id");
            $STH->bindValue(':n', $_POST['pageTitle'], PDO::PARAM_STR);
            $STH->bindValue(':id', $_POST['pageId'], PDO::PARAM_INT);
            $STH->execute();

            $STH = $DBH->prepare("UPDATE " . Alien::getParameter('db_prefix') . "_content_pages SET id_t=:t WHERE id_p=:id");
            $STH->bindValue(':t', $_POST['pageTemplate'], PDO::PARAM_INT);
            $STH->bindValue(':id', $_POST['pageId'], PDO::PARAM_INT);
            $STH->execute();

            $STH = $DBH->prepare("UPDATE " . Alien::getParameter('db_prefix') . "_content_pages SET keywords=:k WHERE id_p=:id");
            $STH->bindValue(':k', $_POST['pageKeywords'], PDO::PARAM_STR);
            $STH->bindValue(':id', $_POST['pageId'], PDO::PARAM_INT);
            $STH->execute();

            $STH = $DBH->prepare("UPDATE " . Alien::getParameter('db_prefix') . "_content_pages SET description=:d WHERE id_p=:id");
            $STH->bindValue(':d', $_POST['pageTitle'], PDO::PARAM_STR);
            $STH->bindValue(':id', $_POST['pageId'], PDO::PARAM_INT);
            $STH->execute();

            $STH = $DBH->prepare("UPDATE " . Alien::getParameter('db_prefix') . "_content_pages SET id_f=:f WHERE id_p=:id");
            $STH->bindValue(':f', $_POST['pageFolder'], PDO::PARAM_STR);
            $STH->bindValue(':id', $_POST['pageId'], PDO::PARAM_INT);
            $STH->execute();

            $STH = $DBH->prepare("UPDATE " . Alien::getParameter('db_prefix') . "_content_pages SET seolink=:s WHERE id_p=:id");
            $STH->bindValue(':s', $_POST['pageSeolink'], PDO::PARAM_STR);
            $STH->bindValue(':id', $_POST['pageId'], PDO::PARAM_INT);
            if (!$STH->execute()) {
                new Notification("Stránka s týmto seolinkom už existuje, zvoľte iný.", "error");
            }

            new Notification("Konfigurácia stránky bola úspešne aktualizovaná.", "success");

            if (Alien::getParameter('allowRedirects')) {
                ob_clean();
                $url = '?page=content&action=editPage&id=' . $_POST['pageId'];
                header("Location: " . $url, true, 301);
                ob_end_flush();
                exit;
            }
        }
    }

    public static function exists($identifier) {
        $DBH = Alien::getDatabaseHandler();
        if (is_numeric($identifier)) {
            $Q = $DBH->prepare('SELECT 1 FROM ' . Alien::getDBPrefix() . '_content_pages WHERE id_p=:i');
            $Q->bindValue(':i', $identifier, PDO::PARAM_INT);
        } else {
            $Q = $DBH->prepare('SELECT 1 FROM ' . Alien::getDBPrefix() . '_content_pages WHERE seolink=:i');
            $Q->bindValue(':i', $identifier, PDO::PARAM_STR);
        }
        $Q->execute();
        return $Q->rowCount() ? true : false;
    }

    public function actionEdit() {
        return BaseController::actionUrl('content', 'editPage', array('id' => $this->id));
    }

    public function actionGoTo() {
        return $this->actionEdit();
    }

    public function actionDrop() {
        return '?content=dropPage&id=' . $this->id;
    }

    public function getIcon() {
        return self::ICON;
    }

    public static function isSeolinkInUse($seolink, $ignoreId = null) {
        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->prepare('SELECT id_p FROM ' . Alien::getDBPrefix() . '_content_pages WHERE seolink=:s LIMIT 1');
        $Q->bindValue(':s', $seolink, PDO::PARAM_STR);
        $Q->execute();
        if (!$Q->rowCount()) {
            return false;
        }
        if ($ignoreId === null && $Q->rowCount()) {
            return true;
        }
        if ($ignoreId !== null) {
            $R = $Q->fetch();
            return $R['id_p'] == $ignoreId ? false : true;
        }
    }

    /*
      public static function renderListOfPages($folder=null){
      $DBH=Alien::getDatabaseHandler();
      global $ALIEN;
      $folder=null; // to len zatial aby netfazulky nevykrikovali warning :)
      $ALIEN['HEADER']=obsahZoznamStranok.('<div class="controlPanel">
      <a href="?page=content&amp;task=pages"><img src="images/icons/refresh.png" title="refresh" alt="refresh"> Refresh</a>
      <a href="?page=content&amp;task=pages&amp;do=new"><img src="images/icons/page_add.png" title="novĂˇ strĂˇnka"> NovĂˇ strĂˇnka</a>
      <a href="#" onClick="javascript: viewMan(\'content.page.man\');"><img src="images/icons/help.png" title="manuĂˇl"> ManuĂˇl</a>
      </div>');
      $STH=$DBH->prepare("SELECT id_p FROM ".Alien::getParameter('db_prefix')."_content_pages");
      $STH->setFetchMode(5);
      $STH->execute();
      if(!$STH->rowCount()){
      echo '<div class="item"><img src="images/icons/information.png"> V tomto prieÄŤinku sa nenachĂˇdzajĂş Ĺľiadne strĂˇnky.</div>';
      }
      while($obj=$STH->fetch()){
      $page=new ContentPage($obj->id_p);
      $page->renderInFolder();
      }
      }
     */
    /*
      public static function renderForm($id=null){

      $new=false;
      if(empty($id)){
      // toto zlyha kedze zatial toto sa generuje z ajaxu a tam neni initnuta autorizacia takze neni opravnenie
      //            Authorization::permissionTest('?page=home',array('page_create'));
      $new=true;
      Alien::setHeading('Nová stránka');
      } else {
      //            Authorization::permissionTest('?page=home',array('page_edit'));
      $page=new ContentPage($id);
      Alien::setHeading(obsahUpravaStranky.': '.$page->getTitle());
      }

      $_SESSION['parentType']='ContentPage';
      if(!$new){
      $_SESSION['parentId']=$page->getID();
      $_SESSION['returnAction']='?page=content&action=editPage&id='.$page->id;
      }

      echo ('<form name="'.($new ? 'new' : 'edit').'PageForm" method="POST" action="" id="pageForm">');
      echo ('<input type="hidden" name="action" value="pageSubmit">');
      echo ('<fieldset><legend>Konfigurácia stránky</legend><table>');

      if(!$new){
      echo ('<input type="hidden" name="pageId" value="'.$page->getId().'">');
      } else {
      echo ('<input type="hidden" name="pageId" value="-1">');
      }

      $avalibleTemplates='';
      $DBH=Alien::getDatabaseHandler();
      foreach($DBH->query('SELECT * FROM '.Alien::getParameter('db_prefix').'_content_templates') as $row){
      $avalibleTemplates.='<option value="'.$row['id_t'].'" '.(!$new && $row['id_t']==$page->getTemplate()->getId() ? 'selected' : '').'>'.$row['name'].'</option>';
      }

      $avalibleFolders='';
      foreach(ContentFolder::getFolderList() as $folder){
      $avalibleFolders.='<option value="'.$folder->getId().'" '.(!$new && $folder->getId()==$page->getFolder() ? 'selected' : '').($new && $_SESSION['folder']==$folder->getId() ? 'selected' : '').'>'.$folder->getName().'</option>';
      }

      echo ('<tr><td><img src="images/icons/title.png" alt=""> '.obsahStrankaTitulok.':</td>
      <td colspan="2"><input type="text" name="pageTitle" value="'.($new ? @$_POST['pageTitle'] : $page->getTitle() ).'" size="35"></td>
      <td rowspan="5" valign="top">Popis:<br><textarea name="pageDescription" style="width: 315px; height: 113px;">'.($new ? @$_POST['pageDescription'] : $page->getDescription()).'</textarea></td></tr>
      <tr><td><img src="images/icons/template.png" alt=""> '.obsahSablona.':</td>
      <td colspan="2"><input type="hidden" name="pageTemplate" id="frmSelTemplate" value="'.($new ? @$_POST['pageTemplate'] : $page->getTemplate()->getId() ).'">
      <select name="pageTemplate">'.$avalibleTemplates.'</select>');
      if(Authorization::permissionTest(null, Array('TEMPLATE_EDIT', 'CONTENT_EDIT'))) echo ('<div id="selTemplate">'.(!$new ? '<a href="?page=content&amp;action=editTemplate&amp;id='.$page->getTemplate()->getId().'" target="_blank">'.$page->getTemplate()->getName().'</a>' : '').'</div>');
      echo ('</td></tr>
      <tr><td><img src="images/icons/link.png" alt=""> '.obsahStrankaSeolink.':</td>
      <td colspan="2"><input type="text" name="pageSeolink" value="'.($new ? @$_POST['pageSeolink'] : $page->getSeolink()).'" size="35"></td></tr>
      <tr><td><img src="images/icons/keywords.png" alt=""> '.obsahStrankaKlucoveSlova.':</td>
      <td colspan="2"><input type="text" name="pageKeywords" value="'.($new ? @$_POST['pageKeywords'] : $page->getKeywords()).'" size="35"></td></tr>
      <tr><td><img src="images/icons/folder.png" alt="" > Adresár:</td>
      <td colspan="2"><select name="pageFolder">'.$avalibleFolders.'</select></td></tr> ');
      echo ('<tr><td colspan="4"><hr></td></tr>');
      $returnAction = 'javascript: window.location=\'?page=content&amp;action=browser&amp;folder='.($new ? $_SESSION['folder'] : $page->getFolder()).'\'';
      echo ('<tr><td colspan="4">
      <div class="button negative" onClick="'.$returnAction.'"><img src="images/icons/cancel.png" alt="cancel"> '.zrusit.'</div>
      <div class="button positive" onClick="javascript: $(\'#pageForm\').submit();"><img src="images/icons/save.png" alt="save"> Uložiť stránku</div>
      '.(!$new ? '<div class="button neutral" onClick="javascript: window.open(\'../'.$page->seolink.'\',\'_blank\');"><img src="images/icons/page_go.png" title="Go to page" alt="Go to">Prejsť na stránku</div>' : '').'
      </tr>');
      echo ('</table></fieldset></form>');

      if($new) return;

      echo ('<br><h2>Obsah stránky:</h2>');
      $varObjects=$page->getVariableItems();
      if(!sizeof($varObjects)){
      echo '<div class="item"><img src="images/icons/information.png"> Šablóna nedefinuje žiadny voliteľný obsah.</div>';
      return;
      }

      $sortTurnOnAction = 'javascript: turnSortableOn();';
      $sortTurnOffAction = 'javascript: turnSortableOff(false);';
      $sortSaveAction = 'javascript: saveSorting(\'ContentPageSortItems\', '.$page->id.');';
      echo ('
      <div class="button neutral" id="sortableTurnOn" onClick="'.$sortTurnOnAction.'"><img src="images/icons/transform_layer.png"> Preusporiadať</div>
      <div class="button negative" id="sortableCancel" style="display: none;" onClick="'.$sortTurnOffAction.'"><img src="images/icons/cancel.png"> Zrušiť zmeny</div>
      <div class="button positive" id="sortableSave" onClick="'.$sortSaveAction.'"><img src="images/icons/save.png"> Uložiť usporiadanie</div>
      ');

      foreach($varObjects as $varObject){

      echo ('<fieldset style="margin-top: 10px;"><legend><img class="toggleHideable less" onClick="javascript: toggleHideable('.$varObject->getContainerId().');" src="images/icons/less.png" style="width: 16px; margin-right: 6px;">'.$varObject->getName().'</legend>');
      echo ('<div id="hideable-'.$varObject->getContainerId().'">');
      echo ('<div id="sortable-'.$varObject->getContainerId().'" class="sortable">');
      $poradie='';

      $varViews=$varObject->getItemViews($page);
      if(!sizeof($varViews)){
      echo '<div class="item"><img src="images/icons/information.png"> V tomto bloku sa nenachachádzajú žiadne objekty.</div>';
      }

      foreach ($varViews as $view) {
      echo ('<div class="ui-state-default" id="'.$view->getId().'">');
      $view->renderInListOfViews();
      echo ('</div>');
      $poradie.=$view->getId().',';
      }

      $poradie=substr($poradie,0,strlen($poradie)-1);
      echo ('</div>'); // sortable
      echo ('<input type="hidden" name="order-sortable-'.$varObject->getContainerId().'" value="'.$poradie.'">');
      if(sizeof($varViews)<$varObject->getLimit()){
      $addViewAction='javascript: window.location=\'?page=content&amp;action=addViewToPage&amp;pid='.$page->id.'&amp;box='.$varObject->getContainerId().'&viewId=-1&typeId=-1\'';
      echo '<div class="button neutral" style="margin-left: 5px; margin-top: 7px; margin-bottom: 10px;" onClick="'.$addViewAction.'"><img src="images/icons/plus.png">&nbsp;Pridat objekt do: <i>'.$varObject->getName().'</i></div>';
      }
      echo ('</div>');
      echo ('</fieldset>');
      }

      return;

      }
     */
    /*     * ******* SPECIFIC METHODS ******************************************************************* */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getSeolink() {
        return $this->seolink;
    }

    public function getTemplate($fetch = false) {
        return $fetch ? new Template($this->template) : $this->template;
    }

    public function getTitle() {
        return $this->getName();
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function getDescription() {
        return $this->description;
    }

    public function isVisible() {
        return (bool) $this->visible;
    }

    public function getFolder() {
        return $this->folder;
    }

    private function getVariableItems() {

        $DBH = Alien::getDatabaseHandler();

        $items = Array();
        $STH = $DBH->prepare('SELECT * FROM ' . Alien::getDBPrefix() . '_content_views cv
            JOIN ' . Alien::getDBPrefix() . '_content_items ci USING(id_i)
            JOIN ' . Alien::getDBPrefix() . '_content_item_types cit ON cit.id_type=ci.id_type
            JOIN ' . Alien::getDBPrefix() . '_content_containers cc ON ci.id_c=cc.id_c
            WHERE classname="' . "VariableItem" . '" && id_t=:idt
            ORDER BY cv.container;');
        $STH->bindValue(':idt', $this->getTemplate()->getId(), PDO::PARAM_INT);
        $STH->execute();
        while ($obj = $STH->fetch()) {
            $items[] = new VariableItem($obj['id_i']);
        }
        return $items;
    }

    /*
      public function getPageRenderedContent($block=null){

      $DBH=Alien::getDatabaseHandler();

      $content='';

      $template=$this->getTemplate();

      $STH=$DBH->prepare("SELECT id_v, container FROM ".Alien::getParameter('db_prefix')."_content_views WHERE id_t=:id ORDER by position");
      $STH->bindValue(':id',$template->getId(),PDO::PARAM_INT);
      $STH->setFetchMode(5);
      $i=1;
      foreach($template->getTemplateBlocks() as $tempBlock => $name){
      if($block!=null && $block!=$name){
      $i++;
      continue;
      }
      if(@$boxes[$tempBlock]!=$name) {
      // ??? uz nvm co presne to ma znacit... mf, funguje :D
      // koment ktory som tu nechal predtym ale nerozumiem mu: toto zabezpeci, aby preslo len tie boxy, ktore maju varobjekt
      //                $i++;
      //                continue;
      }
      $STH->execute();
      while($result=$STH->fetch()){
      if($result->container==$i){
      $view=new ContentItemView($result->id_v);
      //                    $item=ContentItem::getSpecificItem($obj->id_i);
      //                    $item->setVisible($obj->visible);
      //                    $item->setParams(unserialize($obj->params));
      //                    zobrazi aj ostatne itemy - bude lepsie ak hej ale pridat nejako aby to defaultne skryvalo
      if(!($view->getItem() instanceof VariableItem)){
      //                        $content.=$item->getRenderedContent(null);
      $content.=$view->renderView();
      }
      if($view->getItem() instanceof VariableItem && $view->isVisible()){
      $varItems=$view->getItem()->getItemViews($this);
      foreach ($varItems as $varItem) {
      $content.=$varItem->renderView();
      }
      }
      }
      }
      $i++;
      }
      return $content;
      }
     */

    public function isUsed() {
        return false;
    }

    /*
      public function renderControlPanel(){
      $editAction='?page=content&amp;action=editPage&id='.$this->id;
      $dropAction='javascript: if(confirm(\'Naozaj vymazať túto stránku?\')) window.location=\'?page=content&amp;action=dropPage&id='.$this->id.'\'';
      echo('<div style="float: right; display: inline-block; position: relative;">');
      echo ('<a href="../'.$this->seolink.'" target="_blank"><img class="button" src="images/icons/page_go.png" title="Go to page" alt="Go to"></a>');
      if(Authorization::permissionTest(null, Array('CONTENT_EDIT'))) { echo ('<a href="'.$editAction.'"><img class="button" src="images/icons/page_edit.png" title="Edit page" alt="Edit"></a>'); }
      if(Authorization::permissionTest(null, Array('TEMPLATE_EDIT', 'CONTENT_EDIT'))) { echo ('<a href="'.$dropAction.'"><img class="button negative" src="images/icons/page_delete.png" title="Delete page" alt="Edit"></a>'); }
      echo ('</div><br style="clear: right;">');
      }*

      public function getFolderRenderOptions() {
      $options=Array();
      $options['image']='images/icons/page.png';
      $options['name']=$this->name;
      return $options;
      }

      public function renderInFolder() {
      $options=$this->getFolderRenderOptions();
      echo ('<div class="item"><img src="'.$options['image'].'"> <b>'.$options['name'].'</b>');
      $this->renderControlPanel();
      echo ('&nbsp;&nbsp;ID: '.$this->id.'&nbsp;|&nbsp;Typ: Stránka&nbsp;|&nbsp;Seolink: '.$this->seolink);
      echo ('</div>');
      }
     */

    public function getClassName() {
        return 'PageItem';
    }

    /**
     * zoradi itemy
     * @param Array $items
     */
    public function sortItems($items) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare("UPDATE " . Alien::getDBPrefix() . "_content_views SET position=:p WHERE id_v=:id");
        foreach ($items as $string) {
            $i = 1;
            $data = explode(',', $string);
            foreach ($data as $item) {
                $STH->bindValue(':id', $item, PDO::PARAM_INT);
                $STH->bindValue(':p', $i++, PDO::PARAM_INT);
                $STH->execute();
            }
        }
        return;
    }

}

?>
