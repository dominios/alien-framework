<?php

namespace Alien\Models\Authorization;

use DateTime;
use PDO;
use Alien\Application;
use Alien\ActiveRecord;
use Alien\DBConfig;

class User implements ActiveRecord {

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $surname;

    /**
     * @var string
     */
    private $email;

    /**
     * @var DateTime
     */
    private $dateRegistered;

    /**
     * @var bool
     */
    private $activated;

    /**
     * @var DateTime
     */
    private $lastActive;

    /**
     * @var bool
     */
    private $deleted;

    /**
     * @var array
     */
    private $permissions = array();

    /**
     * @var array
     */
    private $groups = array();

    /**
     * @var string
     */
    private $passwordHash;

    public function __construct() {
    }

//    public static function getByLogin($login) {
//        $DBH = Application::getDatabaseHandler();
//        $Q = $DBH->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::USERS) . ' WHERE login=:l;');
//        $Q->bindValue(':l', $login, PDO::PARAM_STR);
//        $Q->execute();
//        if ($Q->rowCount()) {
//            $R = $Q->fetch();
//            return new User($R['id_u'], $R);
//        } else {
//            return false;
//        }
//    }

//    public function update() {
//        $DBH = Application::getDatabaseHandler();
//        $Q = $DBH->prepare('UPDATE ' . DBConfig::table(DBConfig::USERS) . ' SET
//            login=:login, email=:email, activated=:status, ban=:ban, firstname=:fn, surname=:sn
//            WHERE id_u=:id;');
//        $Q->bindValue(':id', $this->id, PDO::PARAM_INT);
//        $Q->bindValue(':login', $this->login, PDO::PARAM_STR);
//        $Q->bindValue(':email', $this->email, PDO::PARAM_STR);
//        $Q->bindValue(':status', $this->getStatus(), PDO::PARAM_INT);
//        $Q->bindValue(':ban', $this->ban, PDO::PARAM_INT);
//        $Q->bindValue(':fn', $this->firstname, PDO::PARAM_STR);
//        $Q->bindValue(':sn', $this->surname, PDO::PARAM_STR);
//        $Q->execute();
//    }

//    public static function exists($id) {
//        $DBH = Application::getDatabaseHandler();
//        $STH = $DBH->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::USERS) . ' WHERE id_u=:i LIMIT 1;');
//        $STH->bindValue(':i', (int) $id, PDO::PARAM_INT);
//        $STH->execute();
//        return $STH->rowCount() ? true : false;
//    }

//    public function isDeletable() {
//        return true;
//    }

//    public function delete() {
//        $DBH = Application::getDatabaseHandler();
//        $DBH->exec('UPDATE ' . DBConfig::table(DBConfig::USERS) . ' SET deleted=1 WHERE id_u="' . (int) $this->id . '";');
//    }

//    public static function create($initialValues) {
//        $DBH = Application::getDatabaseHandler();
//        $Q = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::USERS) . ' (email, dateRegistered) VALUES (:e, :dr);');
//        $Q->bindValue(':e', $initialValues['email'], PDO:: PARAM_STR);
//        $Q->bindValue(':dr', time(), PDO::PARAM_INT);
//        return $Q->execute() ? new User($DBH->lastInsertId()) : false;
//    }

//    public static function getList($fetch = false) {
//        $arr = array();
//        $DBH = Application::getDatabaseHandler();
//        foreach ($DBH->query('SELECT * FROM ' . DBConfig::table(DBConfig::USERS) . ' WHERE deleted!=1') as $R) {
//            $arr[] = $fetch ? new User($R['id_u'], $R) : $R['id_u'];
//        }
//        return $arr;
//    }

    public function resetPassword() {

        $possible_letters = '23456789bcdfghjkmnpqrstvwxyz';
        $number_of_characters = 8;
        $code = '';
        $i = 0;
        while ($i < $number_of_characters) {
            $code .= substr($possible_letters, mt_rand(0, strlen($possible_letters) - 1), 1);
            $i++;
        }
        $this->setPassword($code);

        $view = new \Alien\View('email/resetPassword.php');
        $view->meno = $this->firstname . ' ' . $this->surname;
        $view->heslo = $code;
        $message = $view->renderToString();

        $mail = new \PHPMailer();
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Resetovanie hesla';
        $mail->SetFrom(Application::getParameter('emailDefaultFrom'), Application::getParameter('emailDefailtFromName'));
        $mail->AddAddress($this->email);
        $mail->MsgHTML($message);
        $mail->AltBody = Application::getParameter('emailAltHTMLBody');

        $mail->Send();
    }

    public function getPermissions($fetch = false, $includeGroups = false) {
        $arr = array();
        if (sizeof($this->permissions)) {
            foreach ($this->permissions as $p) {
                if ($p instanceof Permission) {
                    $arr[] = $fetch ? $p : $p->getId();
                } else {
                    $arr[] = $fetch ? new Permission($p) : $p;
                }
            }
        }
        if ($includeGroups === true) {
            $groups = $this->getGroups(true);
            foreach ($groups as $group) {
                $perms = $group->getPermissions($fetch ? true : false);
                foreach ($perms as $p) {
                    $arr[] = $fetch ? $p->getId() : $p;
                }
            }
        }
        return $arr;
    }

    /**
     * Do a permission test upon user
     *
     * @param array $permissions array of <b>ID</b>'s or <b>label</b>'s of permissions, <b>NOT</b> objects.
     * @param string $LOGIC (optional) logic to use for test, if there are more then one permissions. Must be one of <b>OR</b>, <b>AND</b> or <b>XOR</b> logic function. If none was selected, default is AND.
     * @return boolean <b>true</b> if user has needed permission(s), otherwise <b>false</b>.
     */
    public function hasPermission($permissions, $LOGIC = 'AND') {

        $userPermissions = $this->getPermissions(true, true);

        // if ROOT return true, override for everything
        if (in_array(1, $userPermissions)) {
            return true;
        }

        switch (strtoupper($LOGIC)) {
            case 'OR':
                $LOGIC = 'OR';
                break;
            case 'AND':
                $LOGIC = 'AND';
                break;
            default:
                $LOGIC = 'AND';
                break;
        }

        $args = $permissions;
        if (!is_array($args) && (is_string($args) || is_numeric($args))) {
            $temp = $args;
            unset($args);
            $args = array($temp);
        }
        foreach ($args as $arg) {
            if (is_string($arg)) {
                $p = new Permission($arg);
                $arg = $p->getId();
            } else {
                $arg = (int) $arg;
            }
            // $arg - ID of required permission (int)
            if ($LOGIC == 'AND') {
                if (!in_array($arg, $userPermissions)) {
                    return false;
                } else {
                    continue;
                }
            }
            if ($LOGIC == 'OR') {
                if (in_array($arg, $userPermissions)) {
                    return true;
                } else {
                    if ($arg == end($args)) {
                        return false;
                    } else {
                        continue;
                    }
                }
            }
        }
        return $LOGIC === 'AND' ? true : false;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->getLogin();
    }

    public function getLogin() {
        return $this->login;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function getSurname() {
        return $this->surname;
    }

//
//
//    public function getSinceIsMemberOfGroup($group) {
//        $DBH = Application::getDatabaseHandler();
//        $STH = $DBH->prepare('SELECT since FROM ' . DBConfig::table(DBConfig::GROUP_MEMBERS) . ' WHERE id_u=:idu AND id_g=:idg;');
//        $STH->bindValue(':idu', $this->id);
//        $STH->bindValue(':idg', $group->getId());
//        $STH->execute();
//        $result = $STH->fetch();
//        return $result['since'];
//    }
//
//    public function removeGroup(Group $group) {
//        $DBH = Application::getDatabaseHandler();
//        $Q = $DBH->prepare('DELETE FROM ' . DBConfig::table(DBConfig::GROUP_MEMBERS) . ' WHERE id_u=:idu && id_g=:idg LIMIT 1;');
//        $Q->bindValue(':idu', $this->id, PDO::PARAM_INT);
//        $Q->bindValue(':idg', $group->getId(), PDO::PARAM_INT);
//        $Q->execute();
//    }
//
//    public function addGroup(Group $group) {
//        $DBH = Application::getDatabaseHandler();
//        $STH = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::GROUP_MEMBERS) . ' (id_g,id_u,since) VALUES (:idg,:idu,:s);');
//        $STH->bindValue(':idg', $group->getId(), PDO::PARAM_INT);
//        $STH->bindValue(':idu', $this->id, PDO::PARAM_INT);
//        $STH->bindValue(':s', time(), PDO::PARAM_INT);
//        $STH->execute();
//    }
//
//    public function removePermission(Permission $permission) {
//        $DBH = Application::getDatabaseHandler();
//        $STH = $DBH->prepare('DELETE FROM ' . DBConfig::table(DBConfig::USER_PERMISSIONS) . ' WHERE id_u=:idu && id_p=:idp LIMIT 1;');
//        $STH->bindValue(':idp', $permission->getId(), PDO::PARAM_INT);
//        $STH->bindValue(':idu', $this->id, PDO::PARAM_INT);
//        $STH->execute();
//    }
//
//    public function addPermission(Permission $permission) {
//        $DBH = Application::getDatabaseHandler();
//        $STH = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::USER_PERMISSIONS) . ' (id_p,id_u,since) VALUES (:idp,:idu,:s);');
//        $STH->bindValue(':idp', $permission->getId(), PDO::PARAM_INT);
//        $STH->bindValue(':idu', $this->id, PDO::PARAM_INT);
//        $STH->bindValue(':s', time(), PDO::PARAM_INT);
//        $STH->execute();
//    }
//
//    public function isMemberOfGroup($group) {
//        $DBH = Application::getDatabaseHandler();
//        $STH = $DBH->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::GROUP_MEMBERS) . ' WHERE id_u=:idu && id_g=:idg LIMIT 1;');
//        $STH->bindValue(':idu', $this->id);
//        $STH->bindValue(':idg', $group->getId());
//        $STH->execute();
//        $res = $STH->fetch();
//        if ($res) {
//            return true;
//        } else {
//
//            return false;
//        }
//    }

    public function getEmail() {
        return $this->email;
    }

    public function getStatus() {
        return (boolean) $this->activated;
    }

    public function getLastActive($format = null) {
        return $format === null ? $this->lastActive : $this->lastActive->format($format);
    }

    public function getDateRegistered($format = null) {
        return $format === null ? $this->dateRegistered : $this->dateRegistered->format($format);
    }

    public function setLogin($login) {
        $this->login = $login;
        return $this;
    }

//    public function setPassword($password) {
//        $DBH = Application::getDatabaseHandler();
//        $STH = $DBH->prepare("UPDATE " . DBConfig::table(DBConfig::USERS) . " SET password=:pass WHERE id_u=:id LIMIT 1;");
//        $STH->bindValue(':pass', Authorization::generateHash($password), PDO::PARAM_STR);
//        $STH->bindValue(':id', $this->id, PDO::PARAM_INT);
//        $STH->execute();
//    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function setBan($ban) {
        $this->ban = $ban;
        return $this;
    }

    public function setStatus($status) {
        $this->activated = (bool) $status;
        return $this;
    }

    public function isOnline() {
        throw new \RuntimeException("Not implemented yet");
    }

//    public function touch() {
//        $DBH = Application::getDatabaseHandler();
//        $DBH->query('UPDATE ' . DBConfig::table(DBConfig::USERS) . ' SET lastActive = ' . time() . ' WHERE id_u = ' . (int) $this->id . ';');
//    }

    public function setFirstname($firstname) {
        $this->firstname = $firstname;
        return $this;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
        return $this;
    }

    public function getPasswordHash() {
        return $this->passwordHash;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setDateRegistered(DateTime $dateRegistered) {
        $this->dateRegistered = $dateRegistered;
        return $this;
    }

    public function setLastActive(DateTime $lastActive) {
        $this->lastActive = $lastActive;
        return $this;
    }

    public function setActivated($activated) {
        $this->activated = $activated;
        return $this;
    }

    public function setDeleted($deleted) {
        $this->deleted = $deleted;
        return $this;
    }

    public function setPasswordHash($passwordHash) {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    public function setGroups($groups) {
        $this->groups = $groups;
        return $this;
    }

    public function setPermissions($permissions) {
        $this->permissions = $permissions;
        return $this;
    }

    public function getGroups() {
        return $this->groups;
    }

}
