<?php

namespace Alien\Models\Authorization;

use Alien\Alien;
use Alien\DBConfig;
use PDO;

class Authorization {

    private static $instance = null;
    private static $loginTimeOut;
    private static $auth_id;
    private static $user;
    public static $Permissions;

    private function __construct() {

        self::loadPermissions();

        self::$loginTimeOut = Alien::getParameter('loginTimeOut');

        $DBH = Alien::getDatabaseHandler();

        if (@isset($_SESSION['id_auth'])) {
            self::$auth_id = $_SESSION['id_auth'];
            self::$user = self::getCurrentUser();
        } else {
            $STH = $DBH->prepare("INSERT INTO " . DBConfig::table(DBConfig::AUTHORIZATION) . " (id_u, timeout, ip, url) VALUES (:idu, :to, :ip, :url);");
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
        include 'PermissionList.php';
        self::$Permissions = $permission;
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
            $STH = $DBH->prepare("SELECT id_u FROM " . DBConfig::table(DBConfig::AUTHORIZATION) . " WHERE id_auth=:id ORDER BY id_auth DESC LIMIT 1;");
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

    private function validateSession() {

        $DBH = Alien::getDatabaseHandler();

        $STH = $DBH->prepare("SELECT timeout FROM " . DBConfig::table(DBConfig::AUTHORIZATION) . " WHERE id_auth=:id ORDER BY id_auth DESC LIMIT 1;");
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

            $STH = $DBH->prepare('UPDATE ' . DBConfig::table(DBConfig::AUTHORIZATION) . ' SET timeout=:to, url=:url WHERE id_auth = :id;');
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

        $STH = $DBH->prepare('SELECT id_u, login, password, activated, ban FROM ' . DBConfig::table(DBConfig::USERS) . ' WHERE login=:login && deleted!=1 LIMIT 1;');
        $STH->bindValue(':login', $login, PDO::PARAM_STR);
        $STH->execute();
        if (!$STH->rowCount()) {
            return;
        }
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $db_user = $STH->fetch();
        if (Authorization::getInstance()->isLoggedIn($db_user->id_u))
            ;
        if (self::getPasswordHash($password) === $db_user->password) {
            if ($db_user->activated != 1) {
                // error: not activated
            } elseif (time() < $db_user->ban) {
                // error: banned access
            } else {
                // success
                self::$user = new User($db_user->id_u);
                $timeout = time() + self::$loginTimeOut;
                $_SESSION['loginTimeOut'] = $timeout;
                $STH = $DBH->prepare("UPDATE " . DBConfig::table(DBConfig::AUTHORIZATION) . " SET id_u=:id_u, timeout=:to, url=:url WHERE id_auth=:id_a LIMIT 1;");
                $STH->bindValue(':id_a', self::$auth_id, PDO::PARAM_INT);
                $STH->bindValue(':id_u', self::$user->getId(), PDO::PARAM_INT);
                $STH->bindValue(':to', $timeout, PDO::PARAM_INT);
                $STH->bindValue(':url', $_SERVER['REQUEST_URI'], PDO::PARAM_STR);
                $STH->execute();
            }
        } else {
            // error: bad password/login
//            new NoticeFailedLogin($login);
        }
    }

    public function logout() {
        unset($_SESSION);
        session_destroy();
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

    public static function getPasswordHash($passwd) {
        $heslo = '';
        $salt = 'ALiEN_PasSWoRd:SalT--String';
        for ($i = 0; $i < 10; $i++) {
            $heslo .= $salt . $passwd;
            $heslo = sha1($heslo);
        }

        return $heslo;
    }

}
