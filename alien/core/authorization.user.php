<?php

class User {
    
    private $id;
    
    private $login;
    private $email;
    private $date_registered;
    private $activated;
    private $last_active;
    private $ban;
    private $deleted;
    
    private $permissions;
    private $groups;

    public function __construct($id = null, $row = null){
        
        if($row === null && $id === null){ // novy user
            $this->id = 0;
            return;
        }
        elseif($row === null){
            $DBH = Alien::getDatabaseHandler();
            $STH = $DBH->prepare('SELECT * FROM '.Alien::getDBPrefix().'_users WHERE id_u=:i');
            $STH->bindValue(':i', (int)$id, PDO::PARAM_INT);
            $STH->execute();
            if(!$STH->rowCount()) return;
            $row = $STH->fetch();
        }
        
        $this->id = $row['id_u'];
        $this->login = $row['login'];
        $this->email = $row['email'];
        $this->date_registered = (int)$row['date_registered'];
        $this->activated = (bool)$row['activated'];
        $this->last_active = (int)$row['last_active'];
        $this->ban = $row['ban']===null ? false : (int)$row['ban'];
        $this->deleted = (bool)$row['deleted'];
        
        $DBH = Alien::getDatabaseHandler();        
        foreach($DBH->query('SELECT id_g FROM '.Alien::getDBPrefix().'_group_members WHERE id_u='.(int)$this->id) as $group){
            $this->groups[] = $group['id_g'];
        }        
        foreach($DBH->query('SELECT id_p FROM '.Alien::getDBPrefix().'_user_permissions WHERE id_u='.(int)$this->id) as $permission){
            $this->permissions[] = $permission['id_p'];
        }
    }

/* ******** STATIC METHODS ********************************************************************* */
    
    /* TODO */
    public function update(){
        
        if(@isset($_POST['newUserSubmit'])){
            User::create();
            return;
        }

        if(@isset($_POST['editconfirm'])){
            if(User::userExists($_POST['editid'])){
                $user=new User($_POST['editid']);
                $user->setLogin($_POST['editlogin']);
                if($_POST['editpass1']==$_POST['editpass2'] && @isset($_POST['editpass1']) && $_POST['editpass1']!=null) $user->setPassword($_POST['editpass1']);
                $user->setEmail($_POST['editemail']);
                $user->setStatus((int)$_POST['editstatus']);
                $user->setBan($_POST['editban']);
                new Notification("Informácie o používateľovi boli úspešne aktualizované.", "success");
                if(Alien::getParameter("allowRedirects")==true){
                    ob_clean();
                    $url='?page=security&action=viewUsers';
                    header("Location: ".$url, true, 301);
                    ob_end_flush();
                    exit;
                }
            }
        }
    }
        
    /* TODO: OPTIMIZE */
    public static function userExists($id){
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT 1 FROM '.Alien::getDBPrefix().'_users WHERE id_u=:i LIMIT 1');
        $STH->bindValue(':i', (int)$id, PDO::PARAM_INT);
        $STH->execute();
        if($STH->rowCount()){
            return true;
        } else {
            return false;
        }
    }
    
    
//    public static function renderAddGroupList($user){
//        echo ('<h5><img src="images/icons/group_go.png"> '.skupinyClenom.':</h5>');
//        foreach($user->getGroups() as $group){
//            echo ('<div class="item small"><img src="images/icons/group.png">&nbsp;ID: '.$group->getId().'&nbsp;|&nbsp;'.$group->getName().'
//                <div style="float: right; display: inline-block; position: relative;">
//                    <div class="button" onClick="javascript: usersShowGroupInformation('.$group->getId().', '.$user->getId().');"><img src="images/icons/zoom.png"></div>');
//                    if(Authorization::permissionTest(null,array(16))) {
//                        echo ('<div class="button"><a href="?page=users&task=editgroupform&gid='.$group->getId().'"><img src="images/icons/group_go.png" title="ísť na skupinu"></a></div>'); 
//                    }
//                    echo ('<div class="button" onclick="javascript: if(confirm(\''.pouzivateliaVymazatSkupinuPotvrdenie.'\')) usersRemoveGroupOfUser('.$group->getId().','.$user->getId().');"><img src="images/icons/group_delete.png"></div>
//                </div>
//            </div>');
//        } if(!count($user->getGroups())){
//            echo ('<div class="item small"><img src="images/icons/information.png"> '.skupinyNiesuVKategorii.'.</div>');
//        }
//        echo ('<h5><img src="images/icons/group_add.png"> '.skupinyDostupne.':</h5>');            
//        $DBH = Alien::getDatabaseHandler();
//        $STH=$DBH->prepare('SELECT * FROM '.Alien::getParameter('db_prefix').'_groups WHERE id_g NOT IN ( SELECT id_g FROM '.Alien::getParameter('db_prefix').'_group_members WHERE id_u=:idu) ORDER BY groupname;');
//        $STH->bindValue(':idu',$user->getId());
//        $STH->execute();
//        $test=false;
//        while($res=$STH->fetch()){
//            $test=true;
//            $group=new Group($res['id_g']);
//            echo ('<div class="item small"><img src="images/icons/group.png">&nbsp;ID: '.$group->getId().'&nbsp;|&nbsp;'.$group->getName().'
//                <div style="float: right; display: inline-block; position: relative;">
//                    <div class="button" onClick="javascript: usersShowGroupInformation('.$group->getId().', '.$user->getId().');"><img src="images/icons/zoom.png"></div>
//                    <div class="button" onClick="javascript: usersAddGroupOfUser('.$group->getId().','.$user->getId().');"><img src="images/icons/group_add.png"></div>
//                </div>
//            </div>');
//        } if(!$test){
//            echo ('<div class="item small"><img src="images/icons/information.png"> '.skupinyNiesuVKategorii.'.</div>');
//        }
//    }
    
    /* TODO */
    public function drop($id){
        Authorization::permissionTest("?page=users", array('users_delete'));
        if(User::userExists($id)){
            $DBH=Alien::getDatabaseHandler();
            $STH=$DBH->prepare('UPDATE '.Alien::getDBPrefix().'_users SET deleted=1 WHERE id_u=:id');
            $STH->bindValue(':id',$id,PDO::PARAM_INT);
            if($STH->execute()){
                $user=new User($id);
                $logData=Array();
                $logData['user_id']=$user->id;
                $logData['user_name']=$user->getName();
                $logData['deleted_by_id']=Authorization::getCurrentUser()->getId();
                $logData['deleted_by_name']=Authorization::getCurrentUser()->getName();
                $log=new AlienLog(NULL, 104, $logData);
                $log->setImportant(true);
                $log->writeLog();
                new Notification("Používateľ bol nenávratne odstránený zo systému.", "success");
            }
        }
        if(Alien::getParameter('allowRedirects')){
            ob_clean();
            $url='?page=security&action=viewUsers';
            header("Location: ".$url, true , 301);
            ob_end_flush();
            exit;
        }
    }
    
    /**
     * vytvori noveho z POSTu
     */
    public static function create(){
        Authorization::permissionTest("?page=security&action=newUserForm", array('users_create'));
        $write=true;
        $DBH=Alien::getDatabaseHandler();
        if(!empty($_POST['surname'])){
            echo "<script type=\"text/javascript\">alert('GO AWAY SPAM!');</script>";
            $write=FALSE;
            return;
        } else {
            if(@$_POST['newPass1']==''){
                new Notification("Heslo nemôže byť prázdny reťazec.", "warning");
                $write=false;
            }
            if($_POST['newPass1']!=$_POST['newPass2']){
                new Notification("Zadané heslá sa nezhodujú.", "warning");
                $write=FALSE;
            }
            $STH=$DBH->prepare("SELECT id_u FROM ".Alien::getParameter('db_prefix')."_users WHERE login=:login LIMIT 1");
            $STH->bindValue(':login',$_POST['newLogin'],PDO::PARAM_STR);
            $STH->execute();
            if($STH->rowCount()){
                new Notification("Zadaný login sa už využíva, je nutné zvoliť iný.", "warning");
                $write=FALSE;
            }
            $STH=$DBH->prepare("SELECT id_u FROM ".Alien::getParameter('db_prefix')."_users WHERE email=:email LIMIT 1");
            $STH->bindValue(':email',$_POST['newEmail'],PDO::PARAM_STR);
            $STH->execute();
            if($STH->rowCount()){
                new Notification("Tento email sa už využíva, zadať iný.", "warning");
                $write=FALSE;
            }
            $pattern="^.+(\..+)*@.+\..+$";
            if(@!ereg($pattern, $_POST['newEmail'])){
                new Notification("Zadaný reťazec pre email nieje platná emailová adresa.", "warning");
                $write=FALSE;
            }
            if(@md5($_POST['inputCaptcha'])!=$_SESSION['captchaCode']){
                new Notification("Kód bol z obrázka opísaný nesprávne.","warning");
                $write=FALSE;
            }
            if(!$write){
                new Notification("Nového používateľa sa nepodarilo vytvoriť.","error");
                return;
            } else {
                
                $name = $_POST['newLogin'];
                $email = $_POST['newEmail'];
                $pass = $_POST['newPass1'];
                
                $STH=$DBH->prepare('INSERT INTO '.Alien::getDBPrefix().'_users (login, password, email, activated) VALUES (:l, :p, :e, :a)');
                $STH->bindValue(':l', $name, PDO::PARAM_STR);
                $STH->bindValue(':p', md5($pass), PDO::PARAM_STR);
                $STH->bindValue(':e', $email, PDO::PARAM_STR);
                $STH->bindValue(':a', 0, PDO::PARAM_INT);
                $STH->execute();                
                
                $link = "http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/activateregistration.php?id=".md5($DBH->lastInsertId());
                
                $message = '';
                $message .= "<p>Ahoj ".$name."</p>\r\n";
                $message .= "<p>Vaša registrácia na ".Alien::getParameter('weburl').' prebehla úspešne!</p>\r\n';                                
                
                switch(Alien::getParameter('registrationConfirmation')){
                    case 'auto':
//                        $message .= "";
                        $STH=$DBH->prepare('UPDATE '.Alien::getDBPrefix().'_users SET active=1 WHERE id_u=:i');
                        $STH->bindValue(':i', $DBH->lastInsertId(), PDO::PARAM_INT);
                        $STH->execute();
                        break;
                    case 'email':
                        $message .= "<p>Pre potvrdenie Vášku účtu, kliknite na nalsedujúci odkaz:</p>\r\n";
                        $message .= "<p>".$link."</p>\r\n";
                        break;
                    case 'admin':
                        $message .= "<p>Vaša registrácia teraz počká na potvrdenie administrátorom. Do tej doby sa ešte nebudete môcť prihlásiť.</p>\r\n";
                        break;
                }
                
                $message .= "<p>Vaše prihlasovacie údaje:<br>Login: ".$name."<br>Heslo: ".$pass."</p>\r\n";
                $message .= "<p>V prípade akýchkoľvek problémov neváhajte a kontaktujte nás.</p>\r\n";
                $message .= "<p><i>Poznámka: Toto je automaticky generovaný email, prosíme, aby ste na neho neodpovedali.</i></p>\r\n";
                
                if(Alien::getParameter('registrationEmailSend')){
                    $mail = new PHPMailer();
                    $mail->SetFrom(Alien::getParameter('adminAddress'), Alien::getParameter('adminName'));
                    $mail->AddAddress($email);
                    $mail->MsgHTML($message);
                    $mail->Send();
                }                
            }
        }
    }    
    
    
    /**
     * formular
     */
//    public static function renderForm(){
//        if(!is_numeric($_GET['id'])){
//            // nejaky log...
//            return;
//        } elseif(!User::userExists($_GET['id'])) {
//            if(Alien::getParameter("allowRedirects")){
//                ob_clean();
//                new Notification("Zadaný používateľ neexistuje.","warning");
//                $url='?page=security&action=viewUsers';
//                header("Location: ".$url, true, 301);
//                ob_end_flush();
//                exit;
//            }
//            return;
//        }
//        Alien::setHeading('Upraviť profil');
//        $user=new User((int)$_GET['id']*1);
//        $date=$user->getBanDate();
//        if(time()>strtotime($date)) $date="";
//        echo ('<form method="POST" name="userForm"><fieldset><legend>'.$user->getName().'</legend>');
//        echo ('<input type="hidden" name="action" value="updateUser">');
//        echo ('<input type="hidden" name="editid" value="'.$user->getId().'">');
//        echo ('<input type="hidden" name="editconfirm" value="1">');
//        echo ('<table class="noborders">');
//        echo ('<tr><td>'.pouzivateliaLogin.':</td><td><input type="text" name="editlogin" value="'.$user->getName().'"></td></tr>');
//        echo ('<tr><td>'.pouzivateliaEmail.':</td><td><input type="email" name="editemail" value="'.$user->getEmail().'"></td></tr>');
//        echo ('<tr><td>'.pouzivateliaNoveHeslo.':</td><td><input type="password" name="editpass1"></td></tr>');
//        echo ('<tr><td>'.pouzivateliaNoveHesloPotvrdenie.':</td><td><input type="password" name="editpass2"></td></tr>');
//        echo ('<tr><td>'.pouzivateliaStav.':</td><td>
//                <select name="editstatus">');
//                    echo ('<option value="1" '); if($user->getStatus()) echo('selected'); echo('>aktivovaný</option>');
//                    echo ('<option value="0" '); if(!$user->getStatus()) echo('selected'); echo('>neaktivovaný</option>');
//            echo ('</td></tr>');
//        echo ('<tr><td>'.pouzivateliaBanDo.':</td><td><input type="text" name="editban" id="datepicker">
//            <script type="text/javascript">$(document).ready(function(){ $("#datepicker").datepicker("setDate","'.$date.'"); });</script>
//        </td></tr>');
//        echo ('<tr><td>'.pouzivateliaNaposledyOnline.':</td><td>');
//        if($user->isOnline()){
//            echo ('<span style="color: #1dff1d; font-weight: bold;">online</span>');
//        } else {
//            echo ($user->getLastActive());
//        }
//        echo ('</td></tr>');
//        if(Authorization::permissionTest(null, Array('USER_GROUPS'))) echo ('<tr><td colspan="2"><div class="button neutral" onClick="javascript: usersShowGroupManager('.$user->getId().')"><img src="images/icons/group_gear.png"> '.pouzivateliaSpustitSpravcuSkupin.'</div>');
//        echo ('<tr><td colspan="2"><hr></td></tr>');
//        echo ('<tr><td colspan="2">');
//            $cancelAction = 'window.location=\'?page=security&amp;action=viewUsers\';';
//            $saveAction = '$(\'form[name=userForm]\').submit();';
//            echo ('<div class="button negative" onClick="'.$cancelAction.'"><img src="images/icons/cancel.png"> '.zrusit.'</div>');
//            echo ('<div class="button positive" onClick="'.$saveAction.'" style="margin-left: 5px;"><img src="images/icons/save.png"> '.ulozit.'</div>');            
//        echo ('</td></tr>');
//        echo ('</fieldset></form></table>');
//    }   
    
/* ******** SPECIFIC METHODS ********************************************************************** */
    
    // TODO: prepojit s mailami z configu; prerobit cez phpmailer
    /**
     * nedokoncene; reset hesla 
     */
    public function resetPassword(){
        echo 'a';
        $possible_letters = '23456789bcdfghjkmnpqrstvwxyz';
        $number_of_characters = 6;
        $code = ''; 
        $i=0;
        while ($i<$number_of_characters) { 
            $code.=substr($possible_letters, mt_rand(0, strlen($possible_letters)-1), 1);
            $i++;
        }

        $to = $this->details['email'];
        $subject = "Password reset";

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= "From: Dudo Aliens Racing Team <manager@dartteam.sk>\r\n";

        $message="<p>Hello ".$this->details['login']."!</p>\n";
        $message.="<p>Your request to new password is complete, your new password is:</p>\n";
        $message.="<p>".$code."</p>\n";
        $message.="<p><i>Note: this is automatically generated message. Do not reply.</i></p>\n";
        $message.="<p>Dudo Aliens Racing Team</p>\n";

        if(mail($to,$subject,$message,$headers)){
            $this->setPassword($code);
            echo 'email sent to: '.$to.'<br>password changed';
        }

    }
    
    /**
     * Vrati len povolenia ktore MA
     * @param bool $fetch
     */
    public function getPermissions($fetch = false){        
        
        $DBH = Alien::getDatabaseHandler();
        
        $allow_perms = Array();
        
        // najprv cisty user z db
        foreach($DBH->query('SELECT id_p FROM '.Alien::getDBPrefix().'_user_permissions WHERE id_u='.(int)$this->id.' AND value >= 1 AND ( timeout > '.time().' OR timeout IS NULL)') as $row){
            $allow_perms[] = $row['id_p'];
        }
        unset($row);
        // skupiny
        foreach($this->groups as $g){
            foreach($DBH->query('SELECT id_p FROM '.Alien::getDBPrefix().'_group_permissions WHERE id_g='.(int)$g.' AND value >= 1 AND ( timeout > '.time().' OR timeout IS NULL)') as $row){
                if(!in_array($row['id_p'], $allow_perms)){
                    $allow_perms[] = $row['id_p'];
                }
            }
        }
        unset($row);
        if($fetch){
            $perms = Array();
            foreach($allow_perms as $p){
                $perms[] = new Permission($p);
            }
            return $perms;
        } else {
            return $allow_perms;
        }
        
        
    }
    
    /**
     * Vrati VSETKO aj negativne
     * @param bool $fetch
     */
//    public function getAllPermissions($fetch = false){
//
//    }
    
//    public function getPermissions($returnId=false){
//        
//        // už má pridelené, iba ich vrátiť vo forme poľa ID - šetrenie selectami
//        if($this->permissions!=null && $returnId){
//            $array = Array();
//            foreach($this->permissions as $permission){
//                $array[] = $permission->getId();
//            }
//            return $array;
//        }
//        
//        $groups=$this->getGroups();
//        $arr=Array();
//        foreach($groups as $group){
//            // gp - group permissions; p - permissions
//            $gp=$group->getPermissions();
//            foreach($gp as $p){
//                $arr[]=$p;
//            }
//        }
//        $DBH=Alien::getDatabaseHandler();
//        $STH=$DBH->prepare('SELECT * FROM '.Alien::getDBPrefix().'_user_permissions WHERE id_u=:id');
//        $STH->bindValue(':id',$this->getId(),PDO::PARAM_INT);
//        $STH->setFetchMode(PDO::FETCH_OBJ);
//        $STH->execute();
//        if($STH->rowCount()){
//            while($obj=$STH->fetch()){
//                $p=new Permission((int)$obj->id_p);
//                $p->setValue((int)$obj->value);
//                $arr[]=$p;
//            }
//        }
//        
//        // $arr - pole všetkých oprávnení vrátane negatívnych
//        // $newArr - pole po odstránení vymedzujúcich sa oprávnení       
//        $newArr = Array();
//        foreach($arr as $permissionTest){
//            foreach ($arr as $permissionCompare){
//                if($permissionTest->getId()===$permissionCompare->getId()){
//                    if(!$permissionCompare->getValue() || !$permissionTest->getValue()){
//                        continue 2;
//                    }
//                    if(in_array($permissionCompare, $newArr) || in_array($permissionTest, $newArr)){
//                        continue 2;
//                    }
//                }
//            }
//            $newArr[] = $permissionTest;
//        }
//        
//        $arr=$newArr;
//        
//        // vrátiť iba IDčka
//        if($returnId){
//            $retArr = Array();
//            foreach ($arr as $item){
//                $retArr[]=$item->getId();
//            }
//            return $retArr;
//        } else {        
//            return $arr;
//        }
//    }
//    
    /**
     * ci moze citat dany folder
     * @param int $folder idcko
     * @return boolean 
     */
    public function hasFolderReadAccess($folder){
        if($this->hasPermission(array('ROOT','ALL_FOLDERS'),'OR')){
            return true;
        }
//        if($this->hasPermission(array('ALL_FOLDERS'))){
//            return true;
//        }
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare('SELECT view FROM '.Alien::getDBPrefix().'_folder_user_permissions WHERE id_f=:f && id_u=:u');
        $STH->bindValue(':f',$folder,PDO::PARAM_INT);
        $STH->bindValue(':u',$this->id);
        $STH->execute();
        if($STH->rowCount()){
            $result=$STH->fetch();
            if($result['view']==1){
                return true;
            }
        }
        foreach($this->getGroups() as $group){
            if($group->hasFolderReadAccess($folder)){
                return true;
            }
        }
        return false;
    }
    
    /**
     * ci moze updavovat folder
     * @param int $folder idcko
     * @return boolean 
     */
    public function hasFolderModifyAccess($folder){
        if($this->hasPermission(array('ROOT'))){
            return true;
        }
        if($this->hasPermission(array('ALL_FOLDERS'))){
            return true;
        }
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare('SELECT modify FROM '.Alien::getDBPrefix().'_folder_user_permissions WHERE id_f=:f && id_u=:u');
        $STH->bindValue(':f',$folder,PDO::PARAM_INT);
        $STH->bindValue(':u',$this->id);
        $STH->execute();
        if($STH->rowCount()){
            $result=$STH->fetch();
            if($result['modify']==1){
                return true;
            }
        }
        foreach($this->getGroups() as $group){
            if($group->hasFolderModifyAccess($folder)){
                return true;
            }
        }
        return false;
    }
    
    /**
     * Do a permission test upon user
     * 
     * @param array $permissions array of <b>ID</b>'s or <b>label</b>'s of permissions, <b>NOT</b> objects.
     * @param string $LOGIC (optional) logic to use for test, if there are more then one permissions. Must be one of <b>OR</b>, <b>AND</b> or <b>XOR</b> logic function. If none was selected, default is AND.
     * @return boolean <b>true</b> if user has needed permission(s), otherwise <b>false</b>.
     */
    public function hasPermission($permissions, $LOGIC='AND'){
        // if ROOT return true, override for everything
        $userPermissions = $this->getPermissions(true);
        if(in_array(1,$userPermissions)){
            return true;
        }       

        $args=$permissions;        

        switch(strtoupper($LOGIC)){
            case 'OR': $LOGIC = 'OR'; break;
            case 'AND': $LOGIC = 'AND'; break;
            case 'XOR': $LOGIC = 'XOR'; break;
            default: $LOGIC = 'AND'; break;
        }
        
        foreach($args as $arg){
            if(is_string($arg)){
                $p=new Permission($arg);
                $arg=$p->getId();
            } else {
                $arg=(int)$arg;
            }
            // $arg - ID of required permission (int)
            if($LOGIC=='AND'){
               if(!in_array($arg, $userPermissions)){
                    return false;
                } else {
                    continue;
                }
            }
            if($LOGIC=='OR'){
                if(in_array($arg, $userPermissions)){
                    return true;
                } else {
                    if($arg == end($args)){
                        return false;
                    } else {
                        continue;
                    }
                }
            }
            if($LOGIC=='XOR'){
                // DOROBIT !!
            }

        }
        return $LOGIC=='AND' ? true : false;
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){        
        return $this->login;
    }

    public function getGroups($fetch = false){
        $arr = array();
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare("SELECT id_g FROM ".Alien::getDBPrefix()."_group_members WHERE id_u=:id ORDER BY since ASC");
        $STH->bindValue(':id',$this->id);
        $STH->execute();
        while($row=$STH->fetch()){
            $arr[] = $fetch ? new Group($row['id_g']) : $row['id_g'];
        }
        return $arr;
    }
    
    public function getSinceIsMemberOfGroup($group){
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare('SELECT since FROM '.Alien::getDBPrefix().'_group_members WHERE id_u=:idu AND id_g=:idg');
        $STH->bindValue(':idu',$this->id);
        $STH->bindValue(':idg',$group->getId());
        $STH->execute();
        $result=$STH->fetch();
        return $result['since'];
    }
    
    public function removeUserGroup($group){
        $id_u=$this->id;
        $id_g=$group->getId();
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare("SELECT * FROM ".Alien::getDBPrefix()."_group_members WHERE id_u=:id_u && id_g=:id_g LIMIT 1");
        $STH->bindValue('id_u',$id_u);
        $STH->bindValue('id_g',$id_g);
        $STH->execute();
        if(!$STH->rowCount()) return false;
        else {
            $STH=$DBH->prepare("DELETE FROM ".Alien::getDBPrefix()."_group_members WHERE id_u=:id_u && id_g=:id_g LIMIT 1");
            $STH->bindValue('id_u',$id_u);
            $STH->bindValue('id_g',$id_g);
            $STH->execute();
        }
    }
    
    public function addUserGroup($group){
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare('INSERT INTO '.Alien::getDBPrefix().'_group_members (id_g,id_u) VALUES (:idg,:idu)');
        $STH->bindValue(':idg',$group->getId());
        $STH->bindValue(':idu',$this->id);
        $STH->execute();
    }

    public function isMemberOfGroup($group){
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare('SELECT 1 FROM '.Alien::getDBPrefix().'_group_members WHERE id_u=:idu && id_g=:idg LIMIT 1');
        $STH->bindValue(':idu',$this->id);
        $STH->bindValue(':idg',$group->getId());
        $STH->execute();
        $res=$STH->fetch();
        if($res){
            return true;
        } else {
            return false;
        }
    }

    public function getEmail(){
        return $this->email;
    }

    public function getStatus(){
        return (boolean)$this->details['activated'];
    }
    
    public function getLastActive(){
        return $this->last_active;
    }
    
    public function getDateRegistered(){
        return $this->date_registered;
    }

    public function getBanDate(){
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare("SELECT UNIX_TIMESTAMP(ban) AS banstamp FROM ".Alien::getDBPrefix()."_users WHERE id_u=:id LIMIT 1");
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $STH->bindValue(":id",$this->id);
        $STH->execute();
        $result=$STH->fetch()->banstamp;
        if($result==NULL) return NULL;
        else {
            return date("Y-m-d",$result);
        }
    }
    
    public function setLogin($login){
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare("UPDATE ".Alien::getDBPrefix()."_users SET login=:login WHERE id_u=:id LIMIT 1");
        $STH->bindValue(':login',$login);
        $STH->bindValue(':id',  $this->id);
        $STH->execute();
    }
    
    /**
     * Set user's password
     * @global PDO $DBH database handler
     * @param string $pass new password string in <b>non hash form!</b>
     */
    private function setPassword($pass){
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare("UPDATE ".Alien::getDBPrefix()."_users SET password=:pass WHERE id_u=:id LIMIT 1");
        $STH->bindValue(':pass',md5($pass));
        $STH->bindValue(':id',$this->id);
        $STH->execute();
    }
    
    private function setEmail($email){
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare("UPDATE ".Alien::getDBPrefix()."_users SET email=:email WHERE id_u=:id LIMIT 1");
        $STH->bindValue(':email',$email);
        $STH->bindValue(':id',$this->id);
        $STH->execute();
    }
    
    private function setBan($ban){
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare("UPDATE ".Alien::getDBPrefix()."_users SET ban=:ban WHERE id_u=:id LIMIT 1");
        $STH->bindValue(':ban',$ban);
        $STH->bindValue(':id',$this->id);
        $STH->execute();
    }
    
    private function setStatus($status){
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare("UPDATE ".Alien::getDBPrefix()."_users SET activated=:status WHERE id_u=:id LIMIT 1");
        $STH->bindValue(':status',$status);
        $STH->bindValue(':id',$this->id);
        $STH->execute();
    }

    public function isOnline(){
        $DBH=Alien::getDatabaseHandler();
        $STH=$DBH->prepare("SELECT UNIX_TIMESTAMP(timeout) AS time FROM ".Alien::getDBPrefix()."_authorization WHERE id_u=:id ORDER BY id_auth DESC LIMIT 1");
        $STH->bindValue(':id', $this->id);
        $STH->execute();
        // nenasiel sa taky riadok
        if(!$STH->rowCount()){
            return false;
        }        
        $x=$STH->fetch();
        // vyprsal cas
        if($x['time']<time()){
            return false;
        }
        // je online
        else {
            return true;
        }
    }

}

?>
