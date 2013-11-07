<?php

namespace Alien\Authorization;

use PDO;
use Alien\Alien;
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

    public function __construct($id = null, $row = null) {

        if ($row === null && $id === null) { // novy user
            $this->id = null;
            return;
        } elseif ($row === null) {
            $DBH = Alien::getDatabaseHandler();
            $STH = $DBH->prepare('SELECT * FROM ' . DBConfig::table('users') . ' WHERE id_u=:i');
            $STH->bindValue(':i', (int) $id, PDO::PARAM_INT);
            $STH->execute();
            if (!$STH->rowCount())
                return;
            $row = $STH->fetch();
        }

        $this->id = (int) $row['id_u'];
        $this->login = $row['login'];
        $this->email = $row['email'];
        $this->dateRegistered = (int) $row['date_registered'];
        $this->activated = (bool) $row['activated'];
        $this->lastActive = (int) $row['last_active'];
        $this->ban = $row['ban'] === null ? false : (int) $row['ban'];
        $this->deleted = (bool) $row['deleted'];
        $this->firstname = $row['firstname'];
        $this->surname = $row['surname'];

        if (empty($DBH)) {
            $DBH = Alien::getDatabaseHandler();
        }

        $this->groups = array();
        foreach ($DBH->query('SELECT id_g FROM ' . Alien::getDBPrefix() . '_group_members WHERE id_u=' . (int) $this->id) as $group) {
            $this->groups[] = $group['id_g'];
        }

        $this->permissions = array();
        foreach ($DBH->query('SELECT id_p FROM ' . Alien::getDBPrefix() . '_user_permissions WHERE id_u=' . (int) $this->id) as $permission) {
            $this->permissions[] = $permission['id_p'];
        }
    }

    public function update() {
        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->prepare('UPDATE ' . DBConfig::table(DBConfig::USERS) . ' SET
            login=:login, email=:email, activated=:status, ban=:ban, firstname=:fn, surname=:sn
            WHERE id_u=:id');
        $Q->bindValue(':id', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':login', $this->login, PDO::PARAM_STR);
        $Q->bindValue(':email', $this->email, PDO::PARAM_STR);
        $Q->bindValue(':status', $this->activated, PDO::PARAM_INT);
        $Q->bindValue(':ban', $this->ban, PDO::PARAM_INT);
        $Q->bindValue(':fn', $this->firstname, PDO::PARAM_STR);
        $Q->bindValue(':sn', $this->surname, PDO::PARAM_STR);
        $Q->execute();
    }

    public static function exists($id) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::USERS) . ' WHERE id_u=:i LIMIT 1');
        $STH->bindValue(':i', (int) $id, PDO::PARAM_INT);
        $STH->execute();
        return $STH->rowCount() ? true : false;
    }

    public function isDeletable() {
        return true;
    }

    public function delete() {
        $DBH = Alien::getDatabaseHandler();
        $DBH->exec('UPDATE ' . DBConfig::table(DBConfig::USERS) . ' SET deleted=1 WHERE id_u="' . (int) $this->id . '"');
    }

    public static function create($initialValues) {

        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::USERS) . ' (email, date_registered) VALUES (:e, :dr)');
        $Q->bindValue(':e', $initialValues['email'], PDO:: PARAM_STR);
        $Q->bindValue(':dr', time(), PDO::PARAM_INT);
        return $Q->execute() ? new User($DBH->lastInsertId()) : false;






//        Authorization::permissionTest("?page=security&action=newUserForm", array('users_create'));
//        $write = true;
//        $DBH = Alien::getDatabaseHandler();
//        if (!empty($_POST['surname'])) {
//            echo "<script type=\"text/javascript\">alert('GO AWAY SPAM!');</script>";
//            $write = FALSE;
//            return;
//        } else {
//            if (@$_POST['newPass1'] == '') {
//                new Notification("Heslo nemôže byť prázdny reťazec.", "warning");
//                $write = false;
//            }
//            if ($_POST['newPass1'] != $_POST['newPass2']) {
//                new Notification("Zadané heslá sa nezhodujú.", "warning");
//                $write = FALSE;
//            }
//            $STH = $DBH->prepare("SELECT id_u FROM " . Alien::getParameter('db_prefix') . "_users WHERE login=:login LIMIT 1");
//            $STH->bindValue(':login', $_POST['newLogin'], PDO::PARAM_STR);
//            $STH->execute();
//            if ($STH->rowCount()) {
//                new Notification("Zadaný login sa už využíva, je nutné zvoliť iný.", "warning");
//                $write = FALSE;
//            }
//            $STH = $DBH->prepare("SELECT id_u FROM " . Alien::getParameter('db_prefix') . "_users WHERE email=:email LIMIT 1");
//            $STH->bindValue(':email', $_POST['newEmail'], PDO::PARAM_STR);
//            $STH->execute();
//            if ($STH->rowCount()) {
//                new Notification("Tento email sa už využíva, zadať iný.", "warning");
//                $write = FALSE;
//            }
//            $pattern = "^.+(\..+)*@.+\..+$";
//            if (@!ereg($pattern, $_POST['newEmail'])) {
//                new Notification("Zadaný reťazec pre email nieje platná emailová adresa.", "warning");
//                $write = FALSE;
//            }
//            if (@md5($_POST['inputCaptcha']) != $_SESSION['captchaCode']) {
//                new Notification("Kód bol z obrázka opísaný nesprávne.", "warning");
//                $write = FALSE;
//            }
//            if (!$write) {
//                new Notification("Nového používateľa sa nepodarilo vytvoriť.", "error");
//                return;
//            } else {
//
//                $name = $_POST['newLogin'];
//                $email = $_POST['newEmail'];
//                $pass = $_POST['newPass1'];
//
//                $STH = $DBH->prepare('INSERT INTO ' . Alien::getDBPrefix() . '_users (login, password, email, activated) VALUES (:l, :p, :e, :a)');
//                $STH->bindValue(':l', $name, PDO::PARAM_STR);
//                $STH->bindValue(':p', md5($pass), PDO::PARAM_STR);
//                $STH->bindValue(':e', $email, PDO::PARAM_STR);
//                $STH->bindValue(':a', 0, PDO::PARAM_INT);
//                $STH->execute();
//
//                $link = "http://" . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . "/activateregistration.php?id=" . md5($DBH->lastInsertId());
//
//                $message = '';
//                $message .= "<p>Ahoj " . $name . "</p>\r\n";
//                $message .= "<p>Vaša registrácia na " . Alien::getParameter('weburl') . ' prebehla úspešne!</p>\r\n';
//
//                switch (Alien::getParameter('registrationConfirmation')) {
//                    case 'auto':
////                        $message .= "";
//                        $STH = $DBH->prepare('UPDATE ' . Alien::getDBPrefix() . '_users SET active=1 WHERE id_u=:i');
//                        $STH->bindValue(':i', $DBH->lastInsertId(), PDO::PARAM_INT);
//                        $STH->execute();
//                        break;
//                    case 'email':
//                        $message .= "<p>Pre potvrdenie Vášku účtu, kliknite na nalsedujúci odkaz:</p>\r\n";
//                        $message .= "<p>" . $link . "</p>\r\n";
//                        break;
//                    case 'admin':
//                        $message .= "<p>Vaša registrácia teraz počká na potvrdenie administrátorom. Do tej doby sa ešte nebudete môcť prihlásiť.</p>\r\n";
//                        break;
//                }
//
//                $message .= "<p>Vaše prihlasovacie údaje:<br>Login: " . $name . "<br>Heslo: " . $pass . "</p>\r\n";
//                $message .= "<p>V prípade akýchkoľvek problémov neváhajte a kontaktujte nás.</p>\r\n";
//                $message .= "<p><i>Poznámka: Toto je automaticky generovaný email, prosíme, aby ste na neho neodpovedali.</i></p>\r\n";
//
//                if (Alien::getParameter('registrationEmailSend')) {
//                    $mail = new PHPMailer();
//                    $mail->SetFrom(Alien::getParameter('adminAddress'), Alien::getParameter('adminName'));
//                    $mail->AddAddress($email);
//                    $mail->MsgHTML($message);
//                    $mail->Send();
//                }
//            }
//        }
    }

    public static function getList($fetch = false) {
        $arr = array();
        $DBH = Alien::getDatabaseHandler();
        foreach ($DBH->query('SELECT * FROM ' . DBConfig::table(DBConfig::USERS) . ' WHERE deleted!=1') as $R) {
            $arr[] = $fetch ? new User($R['id_u'], $R) : $R['id_u'];
        }
        return $arr;
    }

    /*     * ******* SPECIFIC METHODS ********************************************************************** */

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
        $mail->SetFrom(Alien::getParameter('emailDefaultFrom'), Alien::getParameter('emailDefailtFromName'));
        $mail->AddAddress($this->email);
        $mail->MsgHTML($message);
        $mail->AltBody = Alien::getParameter('emailAltHTMLBody');

        $mail->Send();
    }

    public function getPermissions($fetch = false) {

        $arr = array();
        $DBH = Alien::getDatabaseHandler();
        foreach ($DBH->query('SELECT * FROM ' . DBConfig::table(DBConfig::USER_PERMISSIONS) . ' WHERE id_u=' . $this->id) as $R) {
            $arr[] = $fetch ? new Permission($R['id_p']) : $R['id_p'];
        }
        return $arr;

//        $DBH = Alien::getDatabaseHandler();
//
//        $allow_perms = Array();
//
//        // najprv cisty user z db
//        foreach ($DBH->query('SELECT id_p FROM ' . Alien::getDBPrefix() . '_user_permissions WHERE id_u=' . (int) $this->id . ' AND value >= 1 AND ( timeout > ' . time() . ' OR timeout IS NULL)') as $row) {
//            $allow_perms[] = $row['id_p'];
//        }
//        unset($row);
//        // skupiny
//        foreach ($this->groups as $g) {
//            foreach ($DBH->query('SELECT id_p FROM ' . Alien::getDBPrefix() . '_group_permissions WHERE id_g=' . (int) $g . ' AND value >= 1 AND ( timeout > ' . time() . ' OR timeout IS NULL)') as $row) {
//                if (!in_array($row['id_p'], $allow_perms)) {
//                    $allow_perms[] = $row['id_p'];
//                }
//            }
//        }
//        unset($row);
//        if ($fetch) {
//            $perms = Array();
//            foreach ($allow_perms as $p) {
//                $perms[] = new Permission($p);
//            }
//            return $perms;
//        } else {
//            return $allow_perms;
//        }
    }

    /**
     * ci moze citat dany folder
     * @param int $folder idcko
     * @return boolean
     */
    public function hasFolderReadAccess($folder) {
        if ($this->hasPermission(array('ROOT', 'ALL_FOLDERS'), 'OR')) {
            return true;
        }
//        if($this->hasPermission(array('ALL_FOLDERS'))){
//            return true;
//        }
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT view FROM ' . Alien::getDBPrefix() . '_folder_user_permissions WHERE id_f=:f && id_u=:u');
        $STH->bindValue(':f', $folder, PDO ::PARAM_INT);
        $STH->bindValue(':u', $this->id);
        $STH->execute();
        if ($STH->rowCount()) {
            $result = $STH->fetch();
            if ($result['view'] == 1) {
                return true;
            }
        }
        foreach ($this->getGroups() as $group) {
            if ($group->hasFolderReadAccess($folder)) {
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
    public function hasFolderModifyAccess($folder) {
        if ($this->hasPermission(array('ROOT'))) {
            return true;
        }
        if ($this->hasPermission(array('ALL_FOLDERS'))) {
            return true;
        }
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT modify FROM ' . Alien::getDBPrefix() . '_folder_user_permissions WHERE id_f=:f && id_u=:u');
        $STH->bindValue(':f', $folder, PDO ::PARAM_INT);
        $STH->bindValue(':u', $this->id);
        $STH->execute();
        if ($STH->rowCount()) {
            $result = $STH->fetch();
            if ($result['modify'] == 1) {
                return true;
            }
        }
        foreach ($this->getGroups() as $group) {
            if ($group->hasFolderModifyAccess($folder)) {
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
    public function hasPermission($permissions, $LOGIC = 'AND') {
        // if ROOT return true, override for everything
        $userPermissions = $this->getPermissions(true);
        if (in_array(1, $userPermissions)) {
            return true;
        }

        $args = $permissions;

        switch (strtoupper($LOGIC)) {
            case 'OR': $LOGIC = 'OR';
                break;
            case 'AND': $LOGIC = 'AND';
                break;
            case 'XOR': $LOGIC = 'XOR';
                break;
            default: $LOGIC = 'AND';
                break;
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
            if ($LOGIC == 'XOR') {
// DOROBIT !!
            }
        }
        return

                $LOGIC == 'AND' ? true : false;
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
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT since FROM ' . Alien::getDBPrefix() . '_group_members WHERE id_u=:idu AND id_g=:idg');
        $STH->bindValue(':idu', $this->id);
        $STH->bindValue(':idg', $group->getId());
        $STH->execute();
        $result = $STH->fetch();

        return $result['since'];
    }

    public function removeGroup(Group $group) {
        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->prepare('DELETE FROM ' . DBConfig::table(DBConfig::GROUP_MEMBERS) . ' WHERE id_u=:idu && id_g=:idg LIMIT 1');
        $Q->bindValue(':idu', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':idg', $group->getId(), PDO::PARAM_INT);
        $Q->execute();
    }

    public function addGroup(Group $group) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::GROUP_MEMBERS) . ' (id_g,id_u,since) VALUES (:idg,:idu,:s)');
        $STH->bindValue(':idg', $group->getId(), PDO::PARAM_INT);
        $STH->bindValue(':idu', $this->id, PDO::PARAM_INT);
        $STH->bindValue(':s', time(), PDO::PARAM_INT);
        $STH->execute();
    }

    public function removePermission(Permission $permission) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('DELETE FROM ' . DBConfig::table(DBConfig::USER_PERMISSIONS) . ' WHERE id_u=:idu && id_p=:idp LIMIT 1');
        $STH->bindValue(':idp', $permission->getId(), PDO::PARAM_INT);
        $STH->bindValue(':idu', $this->id, PDO::PARAM_INT);
        $STH->execute();
    }

    public function addPermission(Permission $permission) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::USER_PERMISSIONS) . ' (id_p,id_u,since) VALUES (:idp,:idu,:s)');
        $STH->bindValue(':idp', $permission->getId(), PDO::PARAM_INT);
        $STH->bindValue(':idu', $this->id, PDO::PARAM_INT);
        $STH->bindValue(':s', time(), PDO::PARAM_INT);
        $STH->execute();
    }

    public function isMemberOfGroup($group) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT 1 FROM ' . Alien::getDBPrefix() . '_group_members WHERE id_u=:idu && id_g=:idg LIMIT 1');
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
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare("SELECT UNIX_TIMESTAMP(ban) AS banstamp FROM " . Alien::getDBPrefix() . "_users WHERE id_u=:id LIMIT 1");
        $STH->setFetchMode(PDO ::FETCH_OBJ);
        $STH->bindValue(":id", $this->id);
        $STH->execute();
        $result = $STH->fetch()->banstamp;

        if ($result == NULL)
            return NULL; else {
            return

                    date("Y-m-d", $result);
        }
    }

    public function setLogin($login) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare("UPDATE " . Alien::getDBPrefix() . "_users SET login=:login WHERE id_u=:id LIMIT 1");
        $STH->bindValue(':login', $login);
        $STH->bindValue(':id', $this
                ->id);
        $STH->execute();
    }

    public function setPassword($pass) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare("UPDATE " . DBConfig::table(DBConfig::USERS) . " SET password=:pass WHERE id_u=:id LIMIT 1");
        $STH->bindValue(':pass', Authorization::getPasswordHash($pass), PDO::PARAM_STR);
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
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare("SELECT UNIX_TIMESTAMP(timeout) AS time FROM " . Alien::getDBPrefix() . "_authorization WHERE id_u=:id ORDER BY id_auth DESC LIMIT 1");
        $STH->bindValue(':id', $this->id);
        $STH->execute();
// nenasiel sa taky riadok
        if (!$STH->rowCount()) {
            return false;
        }
        $x = $STH->fetch();
// vyprsal cas
        if ($x['time'] < time()) {
            return false;
        }
// je online
        else {

            return true;
        }
    }

    public function touch() {
        $DBH = Alien::getDatabaseHandler();
        $DBH->query('UPDATE ' . DBConfig::table(DBConfig::USERS) . ' SET last_active = ' . time() . ' WHERE id_u =



        '
                . (int) $this->id)->execute();
    }

    public function setFirstname($firstname) {

        $this->firstname = $firstname;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
    }

}

