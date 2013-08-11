<?php

final class Alien {
    
    public static $SystemImgUrl = 'display/img/';
    
    private static $instance; 
    private $DBH = null;
    private $system_settings;    
//    private static $language = 'sk';
    private $leftMenu='';
    private $mainContent='';
    private $heading='';
    private $actionMenu='';
    
    private $queryCounter = null;
    
    private $console;
    
     
    private function __construct(){
        $this->loadConfig();
        $this->console = AlienConsole::getInstance();
        date_default_timezone_set($this->system_settings['timezone']);
        $this->connectToDatabase($this->system_settings['db_host'], 
                $this->system_settings['db_database'], 
                $this->system_settings['db_username'], 
                $this->system_settings['db_password']
        );
    }
    
    public static final function getInstance(){
        if(!self::$instance){
            self::$instance=new Alien;
        }
        return self::$instance;
    }
    
    /**
     * 
     * @return AlienConsole
     */
    public function getConsole(){
        return $this->console;
    }
    
    /**
     *
     * @global PDO $DBH db handler
     * @global int $queryCounter pocet vykonanych dotazov
     * @param string $host host
     * @param string $database databaza
     * @param string $username meno
     * @param string $password heslo
     * @return PDO database handler
     */
    private final function connectToDatabase($host,$database,$username,$password){
        try {
            # MySQL with PDO_MYSQL
            $DBH = @new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
            $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING ); // ZMENIT POTOM LEN NA EXCEPTION
            $DBH->query('SET NAMES utf8');        
            $sql = $DBH->query('SHOW SESSION STATUS LIKE "Queries";')->fetch();
            $this->queryCounter = $sql['Value'];
        } catch(PDOException $e) {
            header("HTTP/1.1 500 Internal Server Error");
            die('error 500 connect na databazu, prerobit na error hlasku!');
//            include 'alien/error/Error500.html';
            exit;
        }
        /* nastavenie timezone */
        $now = new DateTime(); 
        $mins = $now->getOffset() / 60;
        $sgn = ($mins < 0 ? -1 : 1); 
        $mins = abs($mins); 
        $hrs = floor($mins / 60);
        $mins -= $hrs * 60;
        $offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
        $DBH->exec('SET time_zone="'.$offset.'"');
        $this->console->putMessage('Database handler initialized.');
        $this->DBH = $DBH;
    }

    /**
     * Získa spojenie s databázou
     * @return PDO database handler 
     */
    public static final function getDatabaseHandler(){
        if(self::getInstance()->DBH === null){
            $config=parse_ini_file('config.ini', TRUE);
            Alien::getInstance()->connectToDatabase($config['MYSQL']['db_host'],$config['MYSQL']['db_database'],$config['MYSQL']['db_username'],$config['MYSQL']['db_password']);
        }
        return self::getInstance()->DBH;
    }
    
    /**
     * Získa prefix tabuliek
     * @return string prefix
     */
    public static final function getDBPrefix(){
        return self::getParameter('db_prefix');
    }

    /**
     * nacita konfigiracny subor
     */
    private final function loadConfig(){
        $this->system_settings=parse_ini_file('config.ini');
    }
        
    /**
     * vrati homepage
     * @return ContentPage domovska stranka
     */
    public static function getHomePage($fetch = true){
        $DBH=Alien::getDatabaseHandler();
        $result=$DBH->query('SELECT data FROM '.Alien::getParameter('db_prefix').'_config WHERE param="pages"')->fetch();
        $data = unserialize($result['data']);
        if($fetch){
            return new ContentPage($data['pageHome']);
        } else {
            return $data['pageHome'];
        }
    }
    
    /**
     * vrati URL na cachovany obrazok
     * @return string url
     */
    public static function imageUrl($id, $format='jpg', $resize=0, $width='original', $height=null, $bgcolor='ffffff', $crop=null, $wmark=null, $quality=null){
        
        $url = 'img-'.$id;
        $url.='.'.$format;
        if(file_exists('../cache/'.$url)){
            return $url;
        }
        
        
        /* VALID URL: image.php?id='.$image.'&resize=1&w=250&h=156&q=95&crop=1&wmark=0 */
        $url='image.php?id='.(int)$id;
        $url.='&amp;resize='.(int)$resize;
        if(isset($width)) $url.='&amp;w='.(int)$width;
        if(isset($height))$url.='&amp;h='.(int)$height;
        if(isset($crop)) $url.='&amp;crop='.(int)$crop;
        if(isset($quality)) $url.='&amp;q='.(int)$quality;
        if(isset($crop)) $url.='&amp;wmark='.(int)$wmark;        
        if(isset($format)) $url.='&amp;format='.$format;
        return $url;
//        $url='../cache/';
//        $url.='img-'.$id.'-'.$width.'-'.$height.'-'.$bgcolor.'-'.(int)$crop.'-'.(int)$wmark.'.'.$format;
//        return $url;
    }
    
    /**
     * vygeneruje form z pola
     * @param Array definicia formulara
     */
    public static function generateForm($FormItems){

//        if($itemClassName!='CodeItem' && $itemClassName!='VariableItem'){
//            echo ('<tr>
//                <td><img src="'.$FormItems['itemName']['img'].'"> '.$FormItems['itemName']['label'].':</td>
//                <td><input type="text" name="itemName" size="'.$FormItems['itemName']['size'].'" '.($FormItems['itemName']['value']!=null ? 'value="'.$FormItems['itemName']['value'].'"' : '').'></td>
//            </tr>');
//            unset($FormItems['itemName']);
//            echo ('<tr><td><img src="images/icons/folder.png"> Adresár:</td><td>');
//            $parentFolder=($Item==null ? $_SESSION['folder'] : $Item->getFolder());
//            $options='';
//            foreach(ContentFolder::getFolderList() as $folder){
//                $options.=('<option value="'.$folder->getId().'" '.($folder->getId()==$parentFolder ? 'selected' : '').'>'.$folder->getName().'</option>');
//            }
//            echo ('<select name="itemFolder">'.$options.'</select>');
//            echo ('</td></tr>');
//        }
        
        foreach($FormItems as $Label => $Attrs){
            if($Label=='object'){
                continue;
            }
            if(@$FormItems[$Label]['type']=='text'){
                echo ('<tr>
                    <td><img src="'.$FormItems[$Label]['img'].'"> '.$FormItems[$Label]['label'].':</td>
                    <td><input type="text" name="'.$Label.'" size="'.$FormItems[$Label]['size'].'" '.($FormItems[$Label]['value']!=null ? 'value="'.$FormItems[$Label]['value'].'"' : '').'></td>
                </tr>');
            }
            elseif(@$FormItems[$Label]['type']=='textarea'){
                echo ('<tr>
                    <tr><td colspan="2"><textarea name="'.$Label.'" class="'.($FormItems[$Label]['class']==null ? '' : $FormItems[$Label]['class']).'" cols="85" rows="20">'.($FormItems[$Label]['value']!=null ? ''.$FormItems[$Label]['value'].'' : '').'</textarea></td>
                </tr>');
            }
            elseif(@$FormItems[$Label]['type']=='hidden'){
                echo ('<input type="hidden" name="'.$Label.'" '.($FormItems[$Label]['value']!=null ? 'value="'.$FormItems[$Label]['value'].'"' : '').'>');
            }
            elseif(@$FormItems[$Label]['type']=='select'){
                echo ('<td><img src="'.$FormItems[$Label]['img'].'"> '.$FormItems[$Label]['label'].':</td>');
                $options = '';
                $selectOptions = $FormItems[$Label]['options'];
                foreach($selectOptions['value'] as $key=>$option){                    
                    $options.='<option value="'.$key.'" '.($FormItems[$Label]['value']==$key ? 'selected' : '').'>'.$option.'</option>';
                }
                echo ('<td><select name="'.$Label.'">'.$options.'</select></td>');
                echo ('</tr>');
            }
        }
    }
    
    /**
     * vrati hodnotu konfiguracneho parametra
     * @param string parameter
     * @return mixed hodnota
     */
    public static final function getParameter($param){
        return self::getInstance()->system_settings[$param];
    }

    
    public function getMainMenuItems(){
        $items = Array();
        $items[] = Array('permission'=>'SYSTEM_ACCESS', 'url'=>'?page=system', 'label'=>'Systém', 'img'=>'service.png');
        $items[] = Array('permission'=>'CONTENT_VIEW', 'url'=>'?content=browser', 'label'=>'Obsah', 'img'=>'magazine.png');
        $items[] = Array('permission'=>'USER_VIEW', 'url'=>'?users=viewList', 'label'=>'Používatelia', 'img'=>'user.png');
        $items[] = Array('permission'=>'GROUP_VIEW', 'url'=>'?groups=viewList', 'label'=>'Skupiny', 'img'=>'group.png');
        $items[] = Array('permission'=>null, 'url'=>'#', 'url'=>'?alien=logout', 'label'=>'Odhlásiť', 'img'=>'logout.png');
        return $items;
    }

//        public static final function renderMainMenu(){
////        $user=::getCurrentUser();
//        echo ('<div id="toppanel_menu">');
////        echo "<a href=\"?page=home\"><img src=\"images/icons/home.png\">&nbsp;".tlacitkoHome."</a>";
//        if(Authorization::permissionTest(null,array('SYSTEM_ACCESS'))){
//            echo "<a href=\"?page=system\"><img src=\"images/icons/settings.png\">&nbsp;".tlacitkoSystem."</a>";
//        }
//        if(Authorization::permissionTest(null,array('CONTENT_VIEW'))){
//            echo "<a href=\"?page=content\"><img src=\"images/icons/content.png\">&nbsp;".tlacitkoObsah."</a>";
//        }
//        if(Authorization::permissionTest(null,array('USER_VIEW'))){
//            echo "<a href=\"?page=security\"><img src=\"images/icons/security.png\">&nbsp;Zabezpečenie</a>";
//        }
////        echo "<a href=\"?page=forum\"><img src=\"images/icons/forum.png\">&nbsp;".tlacitkoForum."</a>";
//        echo "<a href=\"#\" onclick=\"javascript: logout();\"><img src=\"images/icons/logout.png\">&nbsp;".'Odhlásiť'."</a>";
//        echo ('</div>'); // toppanelmenu
//    }

    
//    public static final function renderCP(){
//        /* loader */
//        echo ('<div id="loader"><img style="margin-left: -3px;" src="images/loader.gif"><br><div style="margin-top: 5px; margin-left: 0px;">Loading</div></div>');
//        /* overlay & notifications area */
//        echo ('<div id="overlay"></div>');
//        echo ('<div id="window">');
//            echo ('<div id="windowCloser"><img src="images/icons/cancel.png" class="button"></div>');
//            echo ('<div id="windowContent"></div>');
//        echo ('</div>');
//        echo ('<div style="position: absolute; width: 100%;"><div id="notifyArea" style="display: none;"></div></div>');        
//        /* logout form */
//        echo ('<form method="POST" action="" id="logout">');
//        echo ('<input type="hidden" name="logoutFormSubmit" value="Logout">');
//        echo ('</form>');
//    }
    
//    public static final function renderDebugWindow($microtime){
//        if(!self::getParameter('debugMode')) return;
//        echo ('<style type="text/css">#debugWindow {
//            position: fixed;
//            bottom: 2px;
//            margin: 5px;
//            padding: 5px;
//            color: white;
//            background-color: #3a3a3a;
//            opacity: 0.7;
//            display: block;
//            width: 240px;
//        }
//
//        #debugWindow strong {
//            line-height: 18px;
//            border-bottom: 1px solid white;
//            display: block;
//            margin-bottom: 4px;
//        }
//
//        #debugWindow .value {
//            color: #fbf37b;
//            font-weight: bold;
//        }
//        
//        #debugWindow a {
//            color: white;
//        }
//        </style>');
//        
//        echo ('<div id="debugWindow">');
//        echo ('<a href="alien/"><strong>ALiEN CMS v'.Alien::getParameter('version').'</strong></a>');
//        
//        echo ('<br><b>SESSION:</b><br>');
//        foreach($_SESSION as $key => $value){
//            echo '&nbsp;'.$key.' = '.$value.'<br>';
//        }
//        
//        echo ('<br><b>POST:</b><br>');
//        foreach($_POST as $key => $value){
//            echo '&nbsp;'.$key.' = '.$value.'<br>';
//        }
//        
//        echo ('<br><b>GET:</b><br>');
//        foreach($_GET as $key => $value){
//            echo '&nbsp;'.$key.' = '.$value.'<br>';
//        }
//        echo ('<br>');
//        
//        global $queryCounter;
//        $DBH=self::getDatabaseHandler();
//        $STH=$DBH->query('show session status like "Queries";')->fetch();
//        $result=(int)$STH['Value']-(int)$queryCounter;
//        $time=microtime(1)-$microtime;
//        echo 'Memory usage: <span class="value">'.(round(memory_get_usage(true)*9.5367e-7)).' mb</span><br>';
//        echo 'MySQL queries executed: <span class="value">'.$result.'</span><br>';
//        echo 'Time: <span class="value">'.$time.' seconds</span><br>';
//        echo 'AllowRedirects: '.self::getParameter('allowRedirects');
//        
//        echo ('<strong style="border-bottom: 0; border-top: 1px solid white; margin-top: 5px; margin-bottom: 0;">Copyright © Dominik Geršák 2013</strong>');
//        echo ('</div>');
//    }
    
//    public static final function renderLeftMenu(){
//        echo self::getInstance()->leftMenu;
//    }
//    
//    public static final function renderMainContent(){
//        echo self::getInstance()->mainContent;
//    }
//    
//    public static final function renderHeading(){
//        echo self::getInstance()->heading;
//    }
//    
//    public static final function renderActionMenu(){
//        echo self::getInstance()->actionMenu;
//    }
//    
//    public static final function setLeftMenu($content){
//        self::getInstance()->leftMenu=$content;
//    }
//    
//    public static final function setMainContent($content){
//        self::getInstance()->mainContent=$content;
//    }
//    
//    public static final function setHeading($content){
//        self::getInstance()->heading=$content;
//    }
//
//    public static final function setActionMenu($content){
//        self::getInstance()->actionMenu=$content;
//    }
    
}
?>
