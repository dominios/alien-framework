<?php

namespace Alien\Authorization;

use PDO;
use Alien\Alien;
use Alien\DBConfig;

class Group implements \Alien\ActiveRecord {

    private $id;
    private $name;
    private $description;
    private $dateCreated;
    private $members;
    private $permissions;

    public function __construct($id, $row = null) {
        if ($row === null && $id === null) {
            $this->id = null;
            return;
        } elseif ($row === null) {
            $DBH = Alien::getDatabaseHandler();
            $Q = $DBH->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::GROUPS) . ' WHERE id_g=:id');
            $Q->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $Q->execute();
            if (!$Q->rowCount()) {
                $this->id = null;
                return;
            } else {
                $row = $Q->fetch();
            }
        }

        $this->id = (int) $row['id_g'];
        $this->name = $row['name'];
        $this->description = $row['description'];
        $this->dateCreated = (int) $row['dateCreated'];

        if (empty($DBH)) {
            $DBH = Alien::getDatabaseHandler();
        }

        $this->members = array();
        foreach ($DBH->query('SELECT id_u FROM ' . DBConfig::table(DBConfig::GROUP_MEMBERS) . ' WHERE id_g=' . (int) $this->id) as $R) {
            $this->members[] = $R['id_u'];
        }

        $this->permissions = array();
        foreach ($DBH->query('SELECT id_p FROM ' . DBConfig::table(DBConfig::GROUP_PERMISSIONS) . ' WHERE id_g=' . (int) $this->id) as $R) {
            $this->permissions[] = $R['id_p'];
        }
    }

    public function update() {
        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->prepare('UPDATE ' . DBConfig::table(DBConfig::GROUPS) . ' SET name=:n, description=:d WHERE id_g=:id');
        $Q->bindValue(':id', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':n', $this->name, PDO::PARAM_STR);
        $Q->bindValue(':d', $this->description, PDO::PARAM_STR);
        return $Q->execute() ? true : false;
    }

    public static function exists($id) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::GROUPS) . ' WHERE id_g=:i LIMIT 1');
        $STH->bindValue(':i', (int) $id, PDO::PARAM_INT);
        $STH->execute();
        return $STH->rowCount() ? true : false;
    }

    public static function create($initialValues) {
        $DBH = Alien::getDatabaseHandler();
        $Q = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::GROUPS) . ' (dateCreated) VALUES (:dc)');
        $Q->bindValue(':dc', time(), PDO::PARAM_INT);
        return $Q->execute() ? new Group($DBH->lastInsertId()) : false;
    }

    public function delete() {

        if ($this->isDeletable()) {
            $DBH = Alien::getDatabaseHandler();
            $Q = $DBH->exec('DELETE FROM ' . DBConfig::table(DBConfig::GROUPS) . ' WHERE id_g=' . (int) $this->id . ' LIMIT 1');
            return true;
        } else {
            return false;
        }
    }

    public function isDeletable() {
        return count($this->getMembers()) > 0 ? false : true;
    }

    public static function getList($fetch = false) {
        $arr = array();
        $DBH = Alien::getDatabaseHandler();
        foreach ($DBH->query('SELECT * FROM ' . DBConfig::table(DBConfig::GROUPS)) as $R) {
            $arr[] = $fetch ? new Group($R['id_g'], $R) : $R['id_g'];
        }
        return $arr;
    }

    public function getPermissions($fetch = false) {
        if (!count($this->permissions)) {
            return array();
        }
        $ret = array();
        $fetchedPermissions = array();
        foreach ($this->permissions as $perm) {
            if ($fetch && $perm instanceof Permission) {
                $ret[] = $perm;
            } elseif ($fetch && !($perm instanceof Permission)) {
                $f = new Permission($perm);
                $fetchedPermissions[] = $f;
                $ret[] = $f;
            } elseif (!$fetch && $perm instanceof Permission) {
                $ret[] = $perm->getId();
            } else {
                $ret[] = $perm;
            }
        }
        if (sizeof($fetchedPermissions)) {
            $this->permissions = $fetchedPermissions;
        }
        return $ret;
    }

    public function getMembers($fetch = false) {
        if (!count($this->members)) {
            return array();
        }
        $ret = array();
        $fetchedUsers = array();
        foreach ($this->members as $user) {
            if ($fetch && $user instanceof User) {
                $ret[] = $user;
            } elseif ($fetch && !($user instanceof User)) {
                $f = new User($user);
                $fetchedUsers[] = $f;
                $ret[] = $f;
            } elseif (!$fetch && $user instanceof User) {
                $ret[] = $user->getId();
            } else {
                $ret[] = $user;
            }
        }
        if (sizeof($fetchedUsers)) {
            $this->members = $fetchedUsers;
        }
        return $ret;
    }

    public function getName() {
        return $this->name;
    }

    public function getId() {
        return $this->id;
    }

    public function getDateCreated($format = null) {
        return $format === null ? $this->dateCreated : date($format, $this->dateCreated);
    }

    public function getDescription() {
        return $this->description;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function addPermission(Permission $permission) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::GROUP_PERMISSIONS) . ' (id_p,id_g,since) VALUES (:idp,:idg,:s)');
        $STH->bindValue(':idp', $permission->getId(), PDO::PARAM_INT);
        $STH->bindValue(':idg', $this->id, PDO::PARAM_INT);
        $STH->bindValue(':s', time(), PDO::PARAM_INT);
        $STH->execute();
    }

    public function removePermission(Permission $permission) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('DELETE FROM ' . DBConfig::table(DBConfig::GROUP_PERMISSIONS) . ' WHERE id_g=:idg && id_p=:idp LIMIT 1');
        $STH->bindValue(':idp', $permission->getId(), PDO::PARAM_INT);
        $STH->bindValue(':idg', $this->id, PDO::PARAM_INT);
        $STH->execute();
    }

}

?>
