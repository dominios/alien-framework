<?php

class ContentTemplate implements FileItem {

    const ICON = 'template.png';
    const BROWSEABLE = true;

    private $id;
    private $folder;
    private $name;
    private $description;
    private $html_url;
    private $css_url;
    private $config_url;
    private $blocks;

    public function __construct($id = null, $row = null) {
        $new = false;
        if ($row === null) {
            $DBH = Alien::getDatabaseHandler();
            $STH = $DBH->prepare("SELECT * FROM " . Alien::getDBPrefix() . "_content_templates WHERE id_t=:id LIMIT 1");
            $STH->bindValue(':id', $id);
            $STH->execute();
            if (!$STH->rowCount()) {
                $new = true;
            }
            $row = $STH->fetch();
        }
        if ($new) {
            $this->id = null;
            $this->name = '';
            $this->description = '';
            $this->html_url = '';
            $this->css_url = '';
            $this->config_url = '';
            $this->blocks = array();
            $this->folder = new ContentFolder($row['id_f']);
            return;
        }
        $this->id = $row['id_t'];
        $this->name = $row['name'];
        $this->html_url = $row['html_url'];
        $this->css_url = $row['css_url'];
        $this->config_url = $row['config_url'];
        $this->description = $row['description'];
//        $this->blocks=parse_ini_file($row['config_url']);
        $this->fetchBlocks();
//        $this->fetchViews();
    }

    /*     * ******* STATIC METHODS ********************************************************************* */

    public static function getTempatesList($fetch = false) {
        $DBH = Alien::getDatabaseHandler();
        $arr = array();
        $STH = $DBH->prepare("SELECT id_t FROM " . Alien::getDBPrefix() . "_content_templates");
        $STH->execute();
        while ($item = $STH->fetch()) {
            $arr[] = $fetch ? new ContentTemplate($item['id_t']) : $item['id_t'];
        }
        return $arr;
    }

    // DOROBIT PERMISSION TEST; NOTIFIKACIE ASI SA DAJU DAT PREC
//    public static function update(){
//
//        // najprv kontrola spravnosti udajov
//
//        /*
//        $continue = true;
//        if(empty($_POST['templateName']) || $_POST['templateName']==''){
//            new Notification("Názov šablóny nesmie byť prázdny reťazec.", "warning");
//            $continue=false;
//        }
//        if(empty($_POST['templateDesc']) || $_POST['templateDesc']==''){
//            new Notification("Je odporúčané vyplniť pomocný popis.","note");
//        }
//        if(empty($_POST['templateHtml']) || $_POST['templateHtml']==''){
//            new Notification("Šablóna musí mať pridelený svoj zdrojový súbor s HTML kódom.","warning");
//            $continue=false;
//        }
//        if(@strtolower(end(explode('.',$_POST['templateHtml'])))!='php' && $_POST['templateHtml']!=''){
//            new Notification("Zdrojovú súbor šablóny musí mať koncovku .php.","note");
//            $continue=false;
//        }
//        if(empty($_POST['templateCss']) || $_POST['templateCss']==''){
//            new Notification("Odporúča sa priradiť šablóne vlastný CSS štýl.","warning");
//        }
//        if(@strtolower(end(explode('.',$_POST['templateCss'])))!='css' && $_POST['templateCss']!=''){
//            new Notification("Vlastný CSS štýl šablóny musí mať koncovku .css.","note");
//            $continue=false;
//        }
//        if(empty($_POST['templateConfig']) || $_POST['templateConfig']==''){
//            new Notification("Šablóna musí mať prideleý konfiguračný súbor.","warning");
//            $continue=false;
//        }
//        if(@strtolower(end(explode('.',$_POST['templateConfig'])))!='ini' && $_POST['templateConfig']!=''){
//            new Notification("Konfiguračný súbor musí mať koncovku .ini.","note");
//            $continue=false;
//        }
//        if(!$continue){
//            new Notification('Šablónu nieje možné uložiť.', 'error');
//            return;
//        }
//        */
//
//        $DBH=Alien::getDatabaseHandler();
//
//        if(@$_POST['templateId']==0){ // nova sablona
//            $new = true;
//            $STH=$DBH->prepare('INSERT INTO '.Alien::getDBPrefix().'_content_templates (id_f, name, html_url, css_url, config_url, description) VALUES (:f, :n, :html, :css, :ini, :d)');
//        } else {
//            $new = false;
//            $STH=$DBH->prepare('UPDATE '.Alien::getDBPrefix().'_content_templates SET id_f=:f, name=:n, html_url=:html, css_url=:css, config_url=:ini, description=:d WHERE id_t=:id');
//            $STH->bindValue(':id', $_POST['templateId'], PDO::PARAM_INT);
//        }
//
//        $STH->bindValue(':f', $_SESSION['folder'], PDO::PARAM_INT);
//        $STH->bindValue(':n', $_POST['templateName'], PDO::PARAM_STR);
//        $STH->bindValue(':html', $_POST['templatePhp'], PDO::PARAM_STR);
//        $STH->bindValue(':css', $_POST['templateCss'], PDO::PARAM_STR);
//        $STH->bindValue(':ini', $_POST['templateIni'], PDO::PARAM_STR);
//        $STH->bindValue(':d', $_POST['templateDesc'], PDO::PARAM_STR);
//
//        return $STH->execute();
//    }
//    public static function drop($id){
//        $template=new ContentTemplate($id);
//        if($template->isUsed()){
//            new Notification('Nie je možné vymazať šablónu, ktorá sa používa.', Notification::WARNING);
//            new Notification('Nastavte všetky stránky tak, aby nepoužívali túto šablónu.', Notification::INFO);
//            new Notification('Nepodarilo sa odstrániť šablónu.', Notification::ERROR);
//        } else {
//            $DBH=Alien::getDatabaseHandler();
//            $STH=$DBH->prepare('DELETE FROM '.Alien::getDBPrefix().'_content_templates WHERE id_t=:i');
//            $STH->bindValue(':i',$_GET['id'], PDO::PARAM_INT);
//            if($STH->execute()){
//                new Notification('Šablóna bola zmazaná.', 'success');
//            } else {
//                new Notification('Šablónu sa nepodarilo zmazať.', 'error');
//            }
//        }
//        if(Alien::getParameter('allowRedirects')){
//            ob_clean();
//            $url='?content=browser&folder='.$_SESSION['folder'];
//            header('Location: '.$url, false, 301);
//            ob_end_flush();
//            exit;
//        }
//    }

    public static function exists($id) {
        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->query('SELECT 1 FROM ' . Alien::getDBPrefix() . '_content_templates WHERE id_t="' . (int) $id . '"')->execute();
        if ($Q->rowCount()) {
            return true;
        } else {
            return false;
        }
    }

    /*     * ******* SPECIFIC  METHODS ****************************************************************** */

    public function save() {
        $DBH = Alien::getDatabaseHandler();
        $new = $this->id === null ? true : false;
        if ($new) {
            $Q = $DBH->prepare('INSERT INTO ' . Alien::getDBPrefix() . '_content_templates
             (id_f, name, html_url, css_url, config_url, description)
             VALUES (:idf, :name, :html, :css, :ini, :desc);');
        } else {
            $Q = $DBH->prepare('UPDATE ' . Alien::getDBPrefix() . '_content_templates
            SET id_f=:idf, name=:name, html_url=:html, css_url=:css, config_url=:ini, description=:desc WHERE id_t=:i');
            $Q->bindValue(':i', $this->id, PDO::PARAM_INT);
        }
        $Q->bindValue(':idf', $this->folder->getId(), PDO::PARAM_INT);
        $Q->bindValue(':name', $this->name, PDO::PARAM_STR);
        $Q->bindValue(':html', $this->html_url, PDO::PARAM_STR);
        $Q->bindValue(':css', $this->css_url, PDO::PARAM_STR);
        $Q->bindValue(':ini', $this->config_url, PDO::PARAM_STR);
        $Q->bindValue(':desc', $this->description, PDO::PARAM_STR);
        $ret = $Q->execute();
        if ($new && $ret) {
            $this->id = $DBH->lastInsertId();
        }
        return $ret;
    }

    public function drop() {
        if ($this->id === null) {
            return false;
        }
        if ($this->isUsed()) {
            return false;
        }
        $DBH = Alien::getDatabaseHandler();
        return $DBH->query('DELETE FROM ' . Alien::getDBPrefix() . '_content_templates WHERE id_t=' . $this->id . ' LIMIT 1;')->execute();
    }

    public function isBrowseable() {
        return self::BROWSEABLE;
    }

    public function actionEdit() {
        return AlienController::actionUrl('content', 'editTemplate', array('id' => $this->id));
    }

    public function actionGoTo() {
        return $this->actionEdit();
    }

    public function actionDrop() {
        return AlienController::actionUrl('content', 'dropTemplate', array('id' => $this->id));
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getHtmlUrl() {
        return $this->html_url;
    }

    public function getCssUrl() {
        return $this->css_url;
    }

    public function getConfigUrl() {
        return $this->config_url;
    }

    /**
     * ziska pole blokov podla configu
     * @return Array
     */
    public function getBlocks() {
        return $this->blocks;
    }

    public function getDescription() {
        return $this->description;
    }

    public function isUsed() {
        global $DBH;
        $STH = $DBH->prepare('SELECT 1 FROM ' . Alien::getParameter('db_prefix') . '_content_pages WHERE id_t=:id');
        $STH->bindValue(':id', $this->id, PDO::PARAM_INT);
        $STH->execute();
        if ($STH->rowCount()) {
            return true;
        } else {
            return false;
        }
    }

    public function renderControlPanel() {
        echo('<div style="float: right; display: inline-block; position: relative;">');
        $editAction = '?page=content&amp;action=editTemplate&amp;id=' . $this->id;
        $deleteAction = 'javascript: if(confirm(\'Naozaj odstrániť túto šablónu?\')) window.location=\'?page=content&amp;action=dropTemplate&amp;id=' . $this->id . '\'';
        if (Authorization::permissionTest(null, Array('TEMPLATE_EDIT', 'CONTENT_EDIT')))
            echo ('<a href="' . $editAction . '"><img class="button" src="images/icons/layout_edit.png" title="Edit template" alt="Edit"></a>');
        if (Authorization::permissionTest(null, Array('TEMPLATE_EDIT', 'CONTENT_EDIT')))
            echo ($this->isUsed() ? '' : '<a href="#" onClick="' . $deleteAction . '"><img class="button" src="images/icons/layout_delete.png" title="Delete template" alt="delete"></a>');
        echo ('</div><br style="clear: right;">');
    }

    public function getFolderRenderOptions() {
        $options = Array();
        $options['image'] = 'images/icons/template.png';
        $options['name'] = $this->name;
        return $options;
    }

    public function renderInFolder() {
        $options = $this->getFolderRenderOptions();
        echo ('<div class="item"><img src="' . $options['image'] . '"> <b>' . $options['name'] . '</b>');
        $this->renderControlPanel();
        echo ('&nbsp;&nbsp;ID: ' . $this->id . '&nbsp;|&nbsp;' . $this->description . ' &nbsp;|&nbsp;' . obsahZdroj . ': ' . $this->html_url);
        echo ('</div>');
    }

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

    public function getIcon() {
        return self::ICON;
    }

    public static function isTemplateNameInUse($name, $ignoreId = null) {
        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->prepare('SELECT id_t FROM ' . Alien::getDBPrefix() . '_content_templates WHERE name=:n');
        $Q->bindValue(':n', $name, PDO::PARAM_STR);
        $Q->execute();
        if ($ignoreId === null) {
            return $Q->rowCount() ? true : false;
        } else {
            if (!$Q->rowCount())
                return false;
            $R = $Q->fetch();
            return $R['id_t'] == $ignoreId ? false : true;
        }
    }

    private function fetchBlocks() {
        $blocks = Array();
        $ini = parse_ini_file($this->getConfigUrl());
        $i = 1;
        foreach ($ini as $k => $v) {
            $id = (int) substr($k, 3);
            $bl = Array('id' => $id, 'name' => $v, 'items' => Array());
            $blocks[] = $bl;
            $i++;
        }
        $this->blocks = $blocks;
    }

    public function fetchViews() {
        $DBH = ALien::getDatabaseHandler();

        $blocks = $this->blocks;
        $newBlocks = Array();

        foreach ($blocks as $block) {
            $items = Array();
            foreach ($DBH->query('SELECT * FROM ' . Alien::getDBPrefix() . '_content_views WHERE id_c = ' . (int) $block['id'] . ' && id_t=' . $this->getId() . ' ORDER BY position') as $R) {
                $item = ContentItemView::getSpecificView($R['id_v'], $R['id_type'], $R);
                if ($item !== null) {
                    $items[] = $item;
                }
            }
            $newBlocks[] = Array('id' => $block['id'], 'name' => $block['name'], 'items' => $items);
        }

        $this->blocks = $newBlocks;
    }

}
