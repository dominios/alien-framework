<?php

namespace Alien\Rbac;

use Alien\Application;
use Alien\Db\RecordNotFoundException;
use Alien\DBConfig;
use Alien\Di\ServiceLocator;
use Alien\Di\Exception\ServiceNotFoundException;
use PDO;

class Authorization {

    /**
     * Number of seconds until authorization expires
     * @var int
     */
    private $loginTimeOut;

    /**
     * ID of authorization
     * @var int
     */
    private $authId;

    /**
     * @var RoleInterface
     */
    private $user;

    /**
     * @var Permission[]
     */
    public $Permissions;

    /**
     * @var ServiceLocator
     */
    private $serviceManager;

    public function __construct(ServiceLocator $sm) {

        $this->serviceManager = $sm;
        $this->loadPermissions();
        $this->loginTimeOut = 20*60; // @todo cele zle, vela dependencies... Application::getParameter('loginTimeOut');
        $DBH = $this->getServiceManager()->getService('PDO');

        if (@isset($_SESSION['id_auth'])) {

            try {
                $this->authId = $_SESSION['id_auth'];
                $this->user = $this->getCurrentUser();
            } catch (RecordNotFoundException $e) {
                $this->user = new Role();
            }

        } else {
            $STH = $DBH->prepare("INSERT INTO " . DBConfig::table(DBConfig::AUTHORIZATION) . " (id_u, timeout, ip, url) VALUES (:idu, :to, :ip, :url);");
            $STH->bindValue(':idu', 0, PDO::PARAM_INT);
            $STH->bindValue(':to', time() + $this->loginTimeOut, PDO::PARAM_INT);
            $STH->bindValue(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
            $STH->bindValue(':url', $_SERVER['REQUEST_URI'], PDO::PARAM_STR);
            $STH->execute();
            $_SESSION['id_auth'] = $DBH->lastInsertId();
            $this->user = new Role();
            $this->authId = $_SESSION['id_auth'];
        }
//        $this->validateSession();
    }

    public function setServiceManager($serviceManager) {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * @return \Alien\ServiceManager
     */
    public function getServiceManager() {
        return $this->serviceManager;
    }

    /**
     * nacita opravenania zo subora
     * @todo nejaky config?
     * @deprecated
     */
    protected function loadPermissions() {
        $this->Permissions = require_once 'PermissionsList.php';
    }

    public function getLoginTimeOut() {
        return $this->loginTimeOut;
    }

    /**
     * vrati aktualneho usera v session
     * @return RoleInterface user
     */
    public function getCurrentUser() {
        if ($this->user === null) {
            $DBH = $this->getServiceManager()->getService('PDO');
            $STH = $DBH->prepare("SELECT id_u FROM " . DBConfig::table(DBConfig::AUTHORIZATION) . " WHERE id_auth=:id ORDER BY id_auth DESC LIMIT 1;");
            $STH->bindValue(':id', $this->authId);
            $STH->execute();
            $row = $STH->fetch();

            $userDao = $this->getServiceManager()->getDao('UserDao');
            $this->user = $userDao->find($row['id_u']);

            print_r($this->user);

//            $this->user = new Role($row['id_u']);
        }
        return $this->user;
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
                    $str .= $x->getLabel() . '; ';
                }
                new Notification('Potrebné oprávnenia: ' . $str, 'warning');

                new Notification("Prístup odmietnutý.", "error");
                header("Location: " . $location, false, 301);
                ob_end_clean();
                exit;
            }
        }
    }

    public function getCurrentAuthId() {
        return $this->authId;
    }

    private function validateSession() {

//        $DBH = Application::getDatabaseHandler();
        $DBH = $this->getServiceManager()->getService('PDO');

        $STH = $DBH->prepare("SELECT timeout FROM " . DBConfig::table(DBConfig::AUTHORIZATION) . " WHERE id_auth=:id ORDER BY id_auth DESC LIMIT 1;");
        $STH->bindValue(':id', $this->authId, PDO::PARAM_INT);
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
            $STH->bindValue(':id', $this->authId, PDO::PARAM_INT);
            $STH->bindValue(':to', time() + $this->loginTimeOut, PDO::PARAM_INT);
            $STH->bindValue(':url', $_SERVER['REQUEST_URI'], PDO::PARAM_STR);
            $STH->execute();

//            if ($this->getCurrentUser() !== 0) {
////                $this->getCurrentUser()->touch();
//            }
        }
    }

    public function login($login, $password) {

        $db = $this->serviceManager->get('PDO');
        $userDao = $this->serviceManager->getDao('UserDao');

        $user = $userDao->getByLogin($login);
        if ($this->isLoggedIn($user->getId())) {
            return false;
        }

        if (Authorization::validatePassword($password, $user->getPasswordHash())) {
            if (!$user->getStatus()) {
                return false;
            } else {
                $this->user = $user;
                $timeout = time() + $this->loginTimeOut;
                $_SESSION['loginTimeOut'] = $timeout;
                $STH = $db->prepare("UPDATE " . DBConfig::table(DBConfig::AUTHORIZATION) . " SET id_u=:id_u, timeout=:to, url=:url WHERE id_auth=:id_a LIMIT 1;");
                $STH->bindValue(':id_a', $this->authId, PDO::PARAM_INT);
                $STH->bindValue(':id_u', $this->user->getId(), PDO::PARAM_INT);
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
     * @return bool
     */
    public static function validatePassword($password, $hash) {
        /* Regenerating the with an available hash as the options parameter should
         * produce the same hash if the same password is passed.
         */
        return crypt($password, $hash) == $hash;
    }

    public function generatePassword($length = 12) {
        $possible_letters = '23456789bcdfghjkmnpqrstvwxyzBCDFGHJKMNPQRSTVWXYZ_?!$';
        $number_of_characters = $length;
        $code = '';
        $i = 0;
        while ($i < $number_of_characters) {
            $code .= substr($possible_letters, mt_rand(0, strlen($possible_letters) - 1), 1);
            $i++;
        }
        return $code;
    }

}
