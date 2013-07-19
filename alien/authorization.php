<?php

class Authorization {
    
    private static $instance = false;
    private static $loginTimeOut;
    
    private static $auth_id;
    private static $user;
    
    public static $Permissions;

    private function __construct(){
                
        global $PARAM;
        $DBH=Alien::getDatabaseHandler();
        
        self::loadPermissions();
        
        if(@isset($_SESSION['id_auth'])){            
            self::$auth_id=$_SESSION['id_auth'];
            self::$user=self::getCurrentUser();
        } else {
            $STH=$DBH->prepare("INSERT INTO ".Alien::getParameter('db_prefix')."_authorization (id_u, timeout, ip, url) VALUES('0','".date('Y-m-d H:i:s', time()+self::$loginTimeOut)."', '".$PARAM['client_ip']."', '".$PARAM['url']."')");            
            $STH->execute();
            $_SESSION['id_auth']=$DBH->lastInsertId();
            self::$user=new User(0);
            self::$auth_id=$_SESSION['id_auth'];
        }        
        self::loginCheck();
    }
    
    /**
     * nacita opravenania zo subora
     */
    public static function loadPermissions(){
        require_once 'authorization.permission.list.php';
        self::$Permissions=$permission;
        unset($permission);
    }
    
    /**
     * vrati objekt sama seba
     * @return Authorization 
     */
    public static function getAuthorization(){
        if(self::$instance===false){
            self::$instance=new Authorization();
        }
        return self::$instance;
    }
    
    public static function setLoginTimeOut($time){
        self::$loginTimeOut=$time;
    }
    
    public static function getLoginTimeOut(){
        return self::$loginTimeOut;
    }
    
    /**
     * vrati aktualneho usera v session
     * @return User user 
     */
    public static function getCurrentUser(){
        if(self::$user==null){
            $DBH = Alien::getDatabaseHandler();
            $STH=$DBH->prepare("SELECT id_u FROM ".Alien::getParameter('db_prefix')."_authorization WHERE id_auth=:id ORDER BY id_auth DESC LIMIT 1");
            $STH->bindValue(':id',self::$auth_id);
            $STH->execute();
            $result=$STH->fetch();
            self::$user=new User($result['id_u']);
        }
        return self::$user;
    }

    /**
    * Otestovanie aktuálne prihláseného používateľa na oprávnenia
    * 
    * @param string $location kam presmerovať pri chybe
    * @param array $permission testované oprávnenie
    * @param string $logic (optional) logika testovania 
    * @return boolean 
    */
    public static function permissionTest($location, $permissions, $logic='AND'){
        if(self::getCurrentUser()->hasPermission($permissions, $logic)){
            return true;
        } else {
            if($location==null || $location==false){
                
//                $str='';
//                foreach($permissions as $p){
//                    $x=new Permission($p);
//                    $str.=$x->getLabel().' ';
//                }
//                new Notification('Potrebné oprávnenia: '.$str,'note');
                
                return false;
            } else {

                $str='';
                foreach($permissions as $p){
                    $x=new Permission($p);
                    $str.=$x->getLabel().'; ';
                }
                new Notification('Potrebné oprávnenia: '.$str,'warning');
                
                new Notification("Prístup odmietnutý.","error");
                header("Location: ".$location,false,301);
                ob_end_clean();
                exit;
            }
        }
    }

    public static function getCurrentAuthId(){
        return self::$auth_id;
    }

    /**
     * testuje aktualnu session a vstupy POST
     * @global type $PARAM 
     */
    private function loginCheck(){
        
        $DBH=Alien::getDatabaseHandler();
        global $PARAM;
        
        // LOGIN
        if(isset($_POST['loginFormSubmit'])){
            if(!self::$user->getId()){
                self::login($_POST['login'], $_POST['pass']);
                //echo '<script type="text/javascript">alert("test");</script>';
            }
        }
        // LOGOUT
        elseif(isset($_POST['logoutFormSubmit']) && self::$user->getId()){
            self::logout();
        }
        // CHECK IF VALID
        else {
            $STH=$DBH->prepare("SELECT UNIX_TIMESTAMP(timeout) AS time FROM ".Alien::getParameter('db_prefix')."_authorization WHERE id_auth=:id ORDER BY id_auth DESC");
            $STH->bindValue(':id',self::$auth_id);
            $STH->setFetchMode(PDO::FETCH_OBJ);
            $STH->execute();
            $obj=$STH->fetch();
            
            // TIME EXPIRED, AUTOMATICALLY LOGOUT
            if(time()>$obj->time){
                
//                $logData=array('user_id'=>self::$user->getId(), 'user_name'=>self::$user->getName(), 'action'=>'login timeout');
//                $log=new AlienLog(null, 103, $logData);
//                $log->setImportant(true);
//                $log->writeLog();
//                self::logout();
            }
            // STILL VALID, UPDATE WITH NEW DATA
            else {
                // common
                $STH=$DBH->prepare("UPDATE ".Alien::getParameter('db_prefix')."_authorization SET timeout=:time, url=:url WHERE id_auth=:id LIMIT 1");
                $STH->bindValue(':time',date("Y-m-d H:i:s",time()+self::$loginTimeOut));
                $STH->bindValue(':id',self::$auth_id);
                
                $STH->bindValue(':url',$PARAM['url']);
                $STH->execute();
                $_SESSION['loginTimeOut']=time()+self::$loginTimeOut;
                // only for registrated users
                if(self::$user->getId()){
                    $STH=$DBH->prepare("UPDATE ".Alien::getParameter('db_prefix')."_users SET last_active=:la WHERE id_u=:id");
                    $STH->bindValue(':la',date("Y-m-d H:i:s",time()));
                    $STH->bindValue(':id',self::getCurrentUser()->getId());
                    $STH->execute();
                }  
            }
        }
    }
    
    private function login($login, $password){
        $DBH=Alien::getDatabaseHandler();
        global $PARAM;
        $STH=$DBH->prepare('SELECT id_u,login,password,activated,UNIX_TIMESTAMP(ban) AS banstamp FROM '.Alien::getParameter('db_prefix').'_users WHERE login=:login && deleted!=1 LIMIT 1');
        $STH->bindValue(':login',$login);
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $STH->execute();
        $db_user=$STH->fetch();       
        if(md5($password)==@$db_user->password){
            if($db_user->activated!=1){
                // error: not activated
            } elseif($db_user->banstamp>time()){
                // error: banned access
                new NoticeBannedLoginAttempt($db_user->id_u, $db_user->login);
            } else {
                // success
                self::$user=new User($db_user->id_u);
                $timeout=time()+self::$loginTimeOut;
                $_SESSION['loginTimeOut']=$timeout;
                $db_timeout=date("Y-m-d H-i-s",$timeout);
                $STH=$DBH->prepare("UPDATE ".Alien::getParameter('db_prefix')."_authorization SET id_u=:id, timeout=:time, url=:url WHERE id_auth=:id_a LIMIT 1");
                $STH->bindValue(':id_a',self::$auth_id);
                $STH->bindValue(':id',self::$user->getId());
                $STH->bindValue(':time',$db_timeout);
                $STH->bindValue(':url',$PARAM['url']);
                $STH->execute();
                $STH=$DBH->prepare('UPDATE '.Alien::getParameter('db_prefix').'_users SET last_active=:time WHERE id_u=:id');
                $STH->bindValue(':id',self::$user->getId());
                $STH->bindValue(':time',date('Y-m-d H-i-s',time()));
                $STH->execute();

//                $logData=array();                
//                $logData['user_id']=self::$user->getId();
//                $logData['user_name']=self::$user->getName();
//                $logData['action']='login';
//                $log=new AlienLog(null, 101, $logData);
//                $log->setImportant(true);
//                $log->writeLog();
                
                if(Alien::getParameter('allowRedirects')){
                    if(isset($_POST['loginAction'])){
                        $url = $_POST['loginAction'];
                    } else {
                        $url = '?page=home';
                    }
                    ob_clean();
                    header("Location: ".$url, true, 301);
                    ob_end_flush();
                }
            }
        } else {
            // error: bad password/login
//            new NoticeFailedLogin($login);
        }

    }

    private function logout(){
        unset($_SESSION);
        session_destroy();
//        $logData=array();      
//        $logData['user_id']=self::$user->getId();
//        $logData['user_name']=self::$user->getName();
//        $logData['action']='logout';
//        $log=new AlienLog(null, 102, $logData);
//        $log->setImportant(true);
//        $log->writeLog();
        if(Alien::getParameter('allowRedirects')){
            if(isset($_POST['loginAction'])){
                $url = $_POST['loginAction'];
            } else {
                $url = '?page=home';
            }
            ob_clean();
            header("Location: ".$url, true, 301);
            ob_end_flush();
            exit;
        }
    }
}
?>
