<?php

namespace Alien\Authorization;

use Alien\Alien;
use PDO;

class Authorization {

    private static $instance = null;
    private static $loginTimeOut;
    private static $auth_id;
    private static $user;
    public static $Permissions;

    private function __construct() {

        self::$loginTimeOut = Alien::getParameter('loginTimeOut');

//session_destroy();
        $DBH = Alien::getDatabaseHandler();
        self::loadPermissions();
        if (@isset($_SESSION['id_auth'])) {
            self::$auth_id = $_SESSION['id_auth'];
            self::$user = self::getCurrentUser();
        } else {
            $STH = $DBH->prepare("INSERT INTO " . Alien::getDBPrefix() . "_authorization (id_u, timeout, ip, url) VALUES (:idu, :to, :ip, :url)");
            $STH->bindValue(':idu', 0, PDO::PARAM_INT);
            $STH->bindValue(':to', time() + self::$loginTimeOut, PDO::PARAM_INT);
            $STH->bindValue(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
            $STH->bindValue(':url', $_SERVER['REQUEST_URI'], PDO::PARAM_STR);
            $STH->execute();
            $_SESSION['id_auth'] = $DBH->lastInsertId();
            self::$user = new User(0);
            self::$auth_id = $_SESSION['id_auth'];
        }
        self::validateSession();
    }

    /**
     * nacita opravenania zo subora
     */
    public static function loadPermissions() {
        require_once 'PermissionList.php';
        self::$Permissions = @$permission;
        unset($permission);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Authorization();
        }
        return self::$instance;
    }

    public static function getLoginTimeOut() {
        return self::$loginTimeOut;
    }

    /**
     * vrati aktualneho usera v session
     * @return User user
     */
    public static function getCurrentUser() {
        if (self::$user == null) {
            $DBH = Alien::getDatabaseHandler();
            $STH = $DBH->prepare("SELECT id_u FROM " . Alien::getDBPrefix() . "_authorization WHERE id_auth=:id ORDER BY id_auth DESC LIMIT 1");
            $STH->bindValue(':id', self::$auth_id);
            $STH->execute();
            $row = $STH->fetch();
            self::$user = new User($row['id_u']);
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
    public static function permissionTest($location, $permissions, $logic = 'AND') {
        if (self::getCurrentUser()->hasPermission($permissions, $logic)) {
            return true;
        } else {
            if ($location == null || $location == false) {

//                $str='';
//                foreach($permissions as $p){
//                    $x=new Permission($p);
//                    $str.=$x->getLabel().' ';
//                }
//                new Notification('Potrebné oprávnenia: '.$str,'note');

                return false;
            } else {

                $str = '';
                foreach ($permissions as $p) {
                    $x = new Permission($p);
                    $str.=$x->getLabel() . '; ';
                }
                new Notification('Potrebné oprávnenia: ' . $str, 'warning');

                new Notification("Prístup odmietnutý.", "error");
                header("Location: " . $location, false, 301);
                ob_end_clean();
                exit;
            }
        }
    }

    public static function getCurrentAuthId() {
        return self::$auth_id;
    }

    /**
     * testuje aktualnu session
     */
    private function validateSession() {

        $DBH = Alien::getDatabaseHandler();

//        if(empty($_SESSION['id_auth'])){
//            $STH = $DBH->prepare('INSERT INTO '.Alien::getDBPrefix().'_authorization (id_u, timeout, ip, url) VALUES (:id, :to, :ip, :url)');
//            $STH->bindValue(':id', 0, PDO::PARAM_INT);
//            $STH->bindValue(':to', time() + self::$loginTimeOut, PDO::PARAM_INT);
//            $STH->bindValue(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
//            $STH->bindValue(':url', $_SERVER['REQUEST_URI'], PDO::PARAM_STR);
//            $STH->execute();
//        }


        $STH = $DBH->prepare("SELECT timeout FROM " . Alien::getDBPrefix() . "_authorization WHERE id_auth=:id ORDER BY id_auth DESC LIMIT 1");
        $STH->bindValue(':id', self::$auth_id, PDO::PARAM_INT);
        $STH->execute();
        if (!$STH->rowCount()) {
            $this->logout();
            return;
        }
        $R = $STH->fetch();
        if (time() > $R['timeout']) {
            $this->logout();
            return;
        } else {

            $STH = $DBH->prepare('UPDATE ' . Alien::getDBPrefix() . '_authorization SET timeout=:to, url=:url WHERE id_auth = :id;');
            $STH->bindValue(':id', self::$auth_id, PDO::PARAM_INT);
            $STH->bindValue(':to', time() + self::$loginTimeOut, PDO::PARAM_INT);
            $STH->bindValue(':url', $_SERVER['REQUEST_URI'], PDO::PARAM_STR);
            $STH->execute();

            if ($this->getCurrentUser() !== 0) {
                $this->getCurrentUser()->touch();
            }
        }
    }

    public function login($login, $password) {

        $DBH = Alien::getDatabaseHandler();

        $STH = $DBH->prepare('SELECT id_u, login, password, activated, ban FROM ' . Alien::getDBPrefix() . '_users WHERE login=:login && deleted!=1 LIMIT 1');
        $STH->bindValue(':login', $login, PDO::PARAM_STR);
        $STH->execute();
        if (!$STH->rowCount()) {
            return;
        }
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $db_user = $STH->fetch();
        if (Authorization::getInstance()->isLoggedIn($db_user->id_u))
            ;
        if (md5($password) === $db_user->password) {
            if ($db_user->activated != 1) {
                // error: not activated
            } elseif (time() < $db_user->ban) {
                // error: banned access
//                new NoticeBannedLoginAttempt($db_user->id_u, $db_user->login);
            } else {
                // success
                self::$user = new User($db_user->id_u);
                $timeout = time() + self::$loginTimeOut;
                $_SESSION['loginTimeOut'] = $timeout;
                $STH = $DBH->prepare("UPDATE " . Alien::getDBPrefix() . "_authorization SET id_u=:id_u, timeout=:to, url=:url WHERE id_auth=:id_a LIMIT 1");
                $STH->bindValue(':id_a', self::$auth_id, PDO::PARAM_INT);
                $STH->bindValue(':id_u', self::$user->getId(), PDO::PARAM_INT);
                $STH->bindValue(':to', $timeout, PDO::PARAM_INT);
                $STH->bindValue(':url', $_SERVER['REQUEST_URI'], PDO::PARAM_STR);
                $STH->execute();
                $STH = $DBH->prepare('UPDATE ' . Alien::getDBPrefix() . '_users SET last_active=:time WHERE id_u=:id');
                $STH->bindValue(':id', self::$user->getId(), PDO::PARAM_INT);
                $STH->bindValue(':time', time());
                $STH->execute();

//                $logData=array();
//                $logData['user_id']=self::$user->getId();
//                $logData['user_name']=self::$user->getName();
//                $logData['action']='login';
//                $log=new AlienLog(null, 101, $logData);
//                $log->setImportant(true);
//                $log->writeLog();
//                if(Alien::getParameter('allowRedirects')){
//                    if(isset($_POST['loginAction'])){
//                        $url = $_POST['loginAction'];
//                    } else {
//                        $url = '?page=home';
//                    }
//                }
            }
        } else {
            // error: bad password/login
//            new NoticeFailedLogin($login);
        }
    }

    public function logout() {
        unset($_SESSION);
        session_destroy();
//        $logData=array();
//        $logData['user_id']=self::$user->getId();
//        $logData['user_name']=self::$user->getName();
//        $logData['action']='logout';
//        $log=new AlienLog(null, 102, $logData);
//        $log->setImportant(true);
//        $log->writeLog();
//        if(Alien::getParameter('allowRedirects')){
//            if(isset($_POST['loginAction'])){
//                $url = $_POST['loginAction'];
//            } else {
//                $url = '?page=home';
//            }
//            ob_clean();
//            header("Location: ".$url, true, 301);
//            ob_end_flush();
//            exit;
//        }
    }

    public function isLoggedIn($userId = null) {
        if (empty($_SESSION['id_auth'])) {
            return false;
        } else {
            if ($userId !== null) {
                return Authorization::getCurrentUser()->getId() == $userId ? true : false;
            } else {
                return Authorization::getCurrentUser()->getId() > 0 ? true : false;
            }
        }
    }

    /* TODO : saltovanie */

    public function saltPassword($password) {

    }

}
