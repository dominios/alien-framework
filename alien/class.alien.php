<?php

final class Alien {
    
    public static $SystemImgUrl = '/alien/display/img/';
    
    private static $instance; 
    private $DBH = null;
    private $system_settings;
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
     * TODO : nejaky AlienPDO ktory bude vsetko logovat ...
     * @global PDO $DBH db handler
     * @global int $queryCounter pocet vykonanych dotazov
     * @param string $host host
     * @param string $database databaza
     * @param string $username meno
     * @param string $password heslo
     * @return PDO database handler
     */
    private final function connectToDatabase($host,$database,$username,$password){
        include 'class.alien.pdo.php';
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
}