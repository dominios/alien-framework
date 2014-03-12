<?php

namespace Alien\Models\Authorization;

use PDO;
use Alien\Application;
use Alien\ActiveRecord;
use Alien\DBConfig;

class User implements ActiveRecord {

    private $id;
    private $login;
    private $firstname;
    private $surname;
    private $email;
    private $dateRegistered;
    private $activated;
    private $lastActive;
    private $ban;
    private $deleted;
    private $permissions;
    private $groups;
    private $passwordHash;

    public function __construct($id = null, $row = null) {

        if ($row === null && $id === null) { // novy user
            $this->id = null;
            return;
        } elseif ($row === null && $id > 0) {
            $DBH = Application::getDatabaseHandler();
            $STH = $DBH->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::USERS) . ' WHERE id_u=:i;');
            $STH->bindValue(':i', (int) $id, PDO::PARAM_INT);
            $STH->execute();
            if (!$STH->rowCount())
                return;
            $row = $STH->fetch();
        }

        $this->id = (int) $row['id_u'];
        $this->login = $row['login'];
        $this->email = $row['email'];
        $this->dateRegistered = (int) $row['dateRegistered'];
        $this->activated = (bool) $row['activated'];
        $this->lastActive = (int) $row['lastActive'];
        $this->ban = $row['ban'] === null ? false : (int) $row['ban'];
        $this->deleted = (bool) $row['deleted'];
        $this->firstname = $row['firstname'];
        $this->surname = $row['surname'];
        $this->passwordHash = $row['password'];

        if (empty($DBH)) {
            $DBH = Application::getDatabaseHandler();
        }

        $this->groups = array();
        foreach ($DBH->query('SELECT id_g FROM ' . DBConfig::table(DBConfig::GROUP_MEMBERS) . ' WHERE id_u=' . (int) $this->id . ';') as $group) {
            $this->groups[] = $group['id_g'];
        }

        $this->permissions = array();
        foreach ($DBH->query('SELECT id_p FROM ' . DBConfig::table(DBConfig::USER_PERMISSIONS) . ' WHERE id_u=' . (int) $this->id . ';') as $permission) {
            $this->permissions[] = $permission['id_p'];
        }
    }

    public static function getByLogin($login) {
        $DBH = Application::getDatabaseHandler();
        $Q = $DBH->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::USERS) . ' WHERE login=:l;');
        $Q->bindValue(':l', $login, PDO::PARAM_STR);
        $Q->execute();
        if ($Q->rowCount()) {
            $R = $Q->fetch();
            return new User($R['id_u'], $R);
        } else {
            return false;
        }
    }

    public function update() {
        $DBH = Application::getDatabaseHandler();
        $Q = $DBH->prepare('UPDATE ' . DBConfig::table(DBConfig::USERS) . ' SET
            login=:login, email=:email, activated=:status, ban=:ban, firstname=:fn, surname=:sn
            WHERE id_u=:id;');
        $Q->bindValue(':id', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':login', $this->login, PDO::PARAM_STR);
        $Q->bindValue(':email', $this->email, PDO::PARAM_STR);
        $Q->bindValue(':status', $this->getStatus(), PDO::PARAM_INT);
        $Q->bindValue(':ban', $this->ban, PDO::PARAM_INT);
        $Q->bindValue(':fn', $this->firstname, PDO::PARAM_STR);
        $Q->bindValue(':sn', $this->surname, PDO::PARAM_STR);
        $Q->execute();
    }

    public static function exists($id) {
        $DBH = Application::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::USERS) . ' WHERE id_u=:i LIMIT 1;');
        $STH->bindValue(':i', (int) $id, PDO::PARAM_INT);
        $STH->execute();
        return $STH->rowCount() ? true : false;
    }

    public function isDeletable() {
        return true;
    }

    public function delete() {
        $DBH = Application::getDatabaseHandler();
        $DBH->exec('UPDATE ' . DBConfig::table(DBConfig::USERS) . ' SET deleted=1 WHERE id_u="' . (int) $this->id . '";');
    }

    public static function create($initialValues) {
        $DBH = Application::getDatabaseHandler();
        $Q = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::USERS) . ' (email, dateRegistered) VALUES (:e, :dr);');
        $Q->bindValue(':e', $initialValues['email'], PDO:: PARAM_STR);
        $Q->bindValue(':dr', time(), PDO::PARAM_INT);
        return $Q->execute() ? new User($DBH->lastInsertId()) : false;
    }

    public static function getList($fetch = false) {
        $arr = array();
        $DBH = Application::getDatabaseHandler();
        foreach ($DBH->query('SELECT * FROM ' . DBConfig::table(DBConfig::USERS) . ' WHERE deleted!=1') as $R) {
            $arr[] = $fetch ? new User($R['id_u'], $R) : $R['id_u'];
        }
        return $arr;
    }

    public function resetPassword() {

        $possible_letters = '23456789bcdfghjkmnpqrstvwxyz';
        $number_of_characters = 8;
        $code = '';
        $i = 0;
        while ($i < $number_of_characters) {
            $code.=substr($possible_letters, mt_rand(0, strlen($possible_letters) - 1), 1);
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
        foreach ($this->permissions as $p) {
            if ($p instanceof Permission) {
                $arr[] = $fetch ? $p : $p->getId();
            } else {
                $arr[] = $fetch ? new Permission($p) : $p;
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
            case 'OR': $LOGIC = 'OR';
                break;
            case 'AND': $LOGIC = 'AND';
                break;
            default: $LOGIC = 'AND';
                break;
        }

        $args = $permissions;
        if (!is_array($args) && ( is_string($args) || is_numeric($args))) {
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

    public function getGroups($fetch = false) {
        if (!count($this->groups)) {
            return array();
        }
        $ret = array();
        $fetchedGroups = array();
        foreach ($this->groups as $group) {
            if ($fetch && $group instanceof Group) {
                $ret[] = $group;
            } elseif ($fetch && !($group instanceof Group)) {
                $f = new Group($group);
                $fetchedGroups[] = $f;
                $ret[] = $f;
            } elseif (!$fetch && $group instanceof Group) {
                $ret[] = $group->getId();
            } else {
                $ret[] = $group;
            }
        }
        if (sizeof($fetchedGroups)) {
            $this->groups = $fetchedGroups;
        }
        return $ret;
    }

    public function getSinceIsMemberOfGroup($group) {
        $DBH = Application::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT since FROM ' . DBConfig::table(DBConfig::GROUP_MEMBERS) . ' WHERE id_u=:idu AND id_g=:idg;');
        $STH->bindValue(':idu', $this->id);
        $STH->bindValue(':idg', $group->getId());
        $STH->execute();
        $result = $STH->fetch();
        return $result['since'];
    }

    public function removeGroup(Group $group) {
        $DBH = Application::getDatabaseHandler();
        $Q = $DBH->prepare('DELETE FROM ' . DBConfig::table(DBConfig::GROUP_MEMBERS) . ' WHERE id_u=:idu && id_g=:idg LIMIT 1;');
        $Q->bindValue(':idu', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':idg', $group->getId(), PDO::PARAM_INT);
        $Q->execute();
    }

    public function addGroup(Group $group) {
        $DBH = Application::getDatabaseHandler();
        $STH = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::GROUP_MEMBERS) . ' (id_g,id_u,since) VALUES (:idg,:idu,:s);');
        $STH->bindValue(':idg', $group->getId(), PDO::PARAM_INT);
        $STH->bindValue(':idu', $this->id, PDO::PARAM_INT);
        $STH->bindValue(':s', time(), PDO::PARAM_INT);
        $STH->execute();
    }

    public function removePermission(Permission $permission) {
        $DBH = Application::getDatabaseHandler();
        $STH = $DBH->prepare('DELETE FROM ' . DBConfig::table(DBConfig::USER_PERMISSIONS) . ' WHERE id_u=:idu && id_p=:idp LIMIT 1;');
        $STH->bindValue(':idp', $permission->getId(), PDO::PARAM_INT);
        $STH->bindValue(':idu', $this->id, PDO::PARAM_INT);
        $STH->execute();
    }

    public function addPermission(Permission $permission) {
        $DBH = Application::getDatabaseHandler();
        $STH = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::USER_PERMISSIONS) . ' (id_p,id_u,since) VALUES (:idp,:idu,:s);');
        $STH->bindValue(':idp', $permission->getId(), PDO::PARAM_INT);
        $STH->bindValue(':idu', $this->id, PDO::PARAM_INT);
        $STH->bindValue(':s', time(), PDO::PARAM_INT);
        $STH->execute();
    }

    public function isMemberOfGroup($group) {
        $DBH = Application::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::GROUP_MEMBERS) . ' WHERE id_u=:idu && id_g=:idg LIMIT 1;');
        $STH->bindValue(':idu', $this->id);
        $STH->bindValue(':idg', $group->getId());
        $STH->execute();
        $res = $STH->fetch();
        if ($res) {
            return true;
        } else {

            return false;
        }
    }

    public function getEmail() {
        return $this->email;
    }

    public function getStatus() {
        return (boolean) $this->activated;
    }

    public function getLastActive() {
        return $this->lastActive;
    }

    public function getDateRegistered() {
        return $this->dateRegistered;
    }

    public function getBanDate() {
        $DBH = Application::getDatabaseHandler();
        $STH = $DBH->prepare("SELECT UNIX_TIMESTAMP(ban) AS banstamp FROM " . DBConfig::table(DBConfig::USERS) . " WHERE id_u=:id LIMIT 1;");
        $STH->setFetchMode(PDO ::FETCH_OBJ);
        $STH->bindValue(":id", $this->id);
        $STH->execute();
        $result = $STH->fetch()->banstamp;
        if ($result == NULL) {
            return NULL;
        } else {
            return date("Y-m-d", $result);
        }
    }

    public function setLogin($login) {
        $this->login = $login;
    }

    public function setPassword($password) {
        $DBH = Application::getDatabaseHandler();
        $STH = $DBH->prepare("UPDATE " . DBConfig::table(DBConfig::USERS) . " SET password=:pass WHERE id_u=:id LIMIT 1;");
        $STH->bindValue(':pass', Authorization::generateHash($password), PDO::PARAM_STR);
        $STH->bindValue(':id', $this->id, PDO::PARAM_INT);
        $STH->execute();
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setBan($ban) {
        $this->ban = $ban;
    }

    public function setStatus($status) {
        $this->activated = (bool) $status;
    }

    public function isOnline() {
        return false; // TODO proste...
        $DBH = Application::getDatabaseHandler();
        $STH = $DBH->prepare("SELECT UNIX_TIMESTAMP(timeout) AS time FROM " . Application::getDBPrefix() . "_authorization WHERE id_u=:id ORDER BY id_auth DESC LIMIT 1");
        $STH->bindValue(':id', $this->id);
        $STH->execute();
        if (!$STH->rowCount()) {
            return false;
        }
        $x = $STH->fetch();
        if ($x['time'] < time()) {
            return false;
        } else {
            return true;
        }
    }

    public function touch() {
        $DBH = Application::getDatabaseHandler();
        $DBH->query('UPDATE ' . DBConfig::table(DBConfig::USERS) . ' SET lastActive = ' . time() . ' WHERE id_u = ' . (int) $this->id . ';');
    }

    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
    }

    public function getPasswordHash() {
        return $this->passwordHash;
    }

}
