<?php

namespace Alien\Models\Authorization;

use Alien\Application;
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
        self::$loginTimeOut = Application::getParameter('loginTimeOut');
        $DBH = Application::getDatabaseHandler();

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
            $DBH = Application::getDatabaseHandler();
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

        $DBH = Application::getDatabaseHandler();

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

        $DBH = Application::getDatabaseHandler();

        $STH = $DBH->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::USERS) . ' WHERE login=:login && deleted!=1 LIMIT 1;');
        $STH->bindValue(':login', $login, PDO::PARAM_STR);
        $STH->execute();
        if (!$STH->rowCount()) {
            return false;
        }
        $row = $STH->fetch();
        if (User::exists($row['id_u'])) {
            $user = new User($row['id_u'], $row);
        } else {
            return false;
        }

        if (Authorization::getInstance()->isLoggedIn($user->getId())) {
            return false;
        }

        if (Authorization::validatePassword($password, $user->getPasswordHash())) {
            if (!$user->getStatus()) {
                return false;
            } elseif (time() < $user->getBanDate()) {
                return false;
            } else {
                self::$user = $user;
                $timeout = time() + self::$loginTimeOut;
                $_SESSION['loginTimeOut'] = $timeout;
                $STH = $DBH->prepare("UPDATE " . DBConfig::table(DBConfig::AUTHORIZATION) . " SET id_u=:id_u, timeout=:to, url=:url WHERE id_auth=:id_a LIMIT 1;");
                $STH->bindValue(':id_a', self::$auth_id, PDO::PARAM_INT);
                $STH->bindValue(':id_u', self::$user->getId(), PDO::PARAM_INT);
                $STH->bindValue(':to', $timeout, PDO::PARAM_INT);
                $STH->bindValue(':url', $_SERVER['REQUEST_URI'], PDO::PARAM_STR);
                return $STH->execute() ? true : false;
            }
        } else {
            return false;
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

    /**
     * Generate a secure hash for a given password. The cost is passed
     * to the blowfish algorithm. Check the PHP manual page for crypt to
     * find more information about this setting.
     * @param string $password
     * @param int $cost
     * @return string
     */
    public static function generateHash($password, $cost = 11) {
        /* To generate the salt, first generate enough random bytes. Because
         * base64 returns one character for each 6 bits, the we should generate
         * at least 22*6/8=16.5 bytes, so we generate 17. Then we get the first
         * 22 base64 characters
         */
        $salt = substr(base64_encode(openssl_random_pseudo_bytes(17)), 0, 22);
        /* As blowfish takes a salt with the alphabet ./A-Za-z0-9 we have to
         * replace any '+' in the base64 string with '.'. We don't have to do
         * anything about the '=', as this only occurs when the b64 string is
         * padded, which is always after the first 22 characters.
         */
        $salt = str_replace("+", ".", $salt);
        /* Next, create a string that will be passed to crypt, containing all
         * of the settings, separated by dollar signs
         */
        $param = '$' . implode('$', array(
                    "2y", //select the most secure version of blowfish (>=PHP 5.3.7)
                    str_pad($cost, 2, "0", STR_PAD_LEFT), //add the cost in two digits
                    $salt //add the salt
        ));

        //now do the actual hashing
        return crypt($password, $param);
    }

    /**
     * Check the password against a hash generated by the generate_hash
     * function.
     * @param string $password
     * @param string $hash
     * @return type
     */
    public static function validatePassword($password, $hash) {
        /* Regenerating the with an available hash as the options parameter should
         * produce the same hash if the same password is passed.
         */
        return crypt($password, $hash) == $hash;
    }

}
