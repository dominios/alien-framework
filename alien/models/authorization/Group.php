<?php

namespace Alien\Models\Authorization;

use Alien\ActiveRecord;
use DateTime;
use PDO;
use Alien\Application;
use Alien\DBConfig;

class Group implements ActiveRecord {

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var DateTime
     */
    private $dateCreated;

    /**
     * @var User[]
     */
    private $members;

    /**
     * @var Permission[]
     */
    private $permissions;

    public function __construct() {
    }

    public function setDateCreated($dateCreated) {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated($format = null) {
        return $format === null ? $this->dateCreated : $this->dateCreated->format($format);
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function isDeletable() {
        return count($this->getMembers()) > 0 ? false : true;
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

    public function addPermission(Permission $permission) {
        $DBH = Application::getDatabaseHandler();
        $STH = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::GROUP_PERMISSIONS) . ' (id_p,id_g,since) VALUES (:idp,:idg,:s)');
        $STH->bindValue(':idp', $permission->getId(), PDO::PARAM_INT);
        $STH->bindValue(':idg', $this->id, PDO::PARAM_INT);
        $STH->bindValue(':s', time(), PDO::PARAM_INT);
        $STH->execute();
    }

    public function removePermission(Permission $permission) {
        $DBH = Application::getDatabaseHandler();
        $STH = $DBH->prepare('DELETE FROM ' . DBConfig::table(DBConfig::GROUP_PERMISSIONS) . ' WHERE id_g=:idg && id_p=:idp LIMIT 1');
        $STH->bindValue(':idp', $permission->getId(), PDO::PARAM_INT);
        $STH->bindValue(':idg', $this->id, PDO::PARAM_INT);
        $STH->execute();
    }

}
