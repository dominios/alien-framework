<?php

namespace Alien\Models\Authorization;

use Alien\DBRecord;
use Alien\Db\CRUDDaoImpl;
use Alien\DBConfig;
use Alien\Di\ServiceManager;
use Alien\TableViewInterface;
use DateTime;
use InvalidArgumentException;
use PDO;
use PDOStatement;

class UserDao extends CRUDDaoImpl implements TableViewInterface {

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function __construct(PDO $connection, ServiceManager $sm) {
        parent::__construct($connection);
        $this->serviceManager = $sm;
    }

    /**
     * @return PDOStatement
     */
    protected function prepareCreateStatement() {
        $conn = $this->getConnection();
        $stmt = $conn->prepare('INSERT INTO ' . DBConfig::table(DBConfig::USERS) . ' (email, dateRegistered) VALUES (:e, :dr);');
        $stmt->bindValue(':e', '', PDO:: PARAM_STR);
        $stmt->bindValue(':dr', time(), PDO::PARAM_INT);
        return $stmt;
    }

    /**
     * @param array $result
     * @return DBRecord
     */
    protected function createFromResultSet(array $result) {
        $user = new User();
        $user->setId($result['id_u']);
        $user->setLogin($result['login']);
        $user->setEmail($result['email']);
        $user->setDateRegistered(new DateTime("@" . $result['dateRegistered']));
        $user->setActivated($result['activated']);
        $user->setLastActive(new DateTime('@' . $result['lastActive']));
        $user->setDeleted($result['deleted']);
        $user->setFirstname($result['firstname']);
        $user->setSurname($result['surname']);
        $user->setPasswordHash($result['password']);

        $groupDao = $this->serviceManager->getDao('GroupDao');
        $user->setGroups($groupDao->getUserGroups($user));
//
        $permissions = array();
        $stmt = $this->getConnection()->prepare('SELECT id_p FROM ' . DBConfig::table(DBConfig::USER_PERMISSIONS) . ' WHERE id_u=:id;');
        $stmt->bindValue(':id', $user->getId());
        $stmt->execute();
        foreach ($stmt->fetchAll() as $permission) {
            $permissions[] = $permission['id_p'];
        }
        $user->setPermissions($permissions);

//        $user->id = (int) $row['id_u'];
//        $user->login = $row['login'];
//        $user->email = $row['email'];
//        $user->dateRegistered = (int) $row['dateRegistered'];
//        $user->activated = (bool) $row['activated'];
//        $user->lastActive = (int) $row['lastActive'];
//        $user->ban = $row['ban'] === null ? false : (int) $row['ban'];
//        $user->deleted = (bool) $row['deleted'];
//        $user->firstname = $row['firstname'];
//        $user->surname = $row['surname'];
//        $user->passwordHash = $row['password'];
        return $user;
    }

    /**
     * @return PDOStatement
     */
    protected function prepareSelectAllStatement($filter = null) {
        $conn = $this->getConnection();
        $and = array(true);
        $and[] = 'deleted <> 1';
        if ($filter) {
            $and[] .= 'id_g = ' . $filter;
        }
        return $conn->prepare('SELECT * FROM test_users u JOIN test_group_members gm ON gm.id_u = u.id_u WHERE ' . implode(' AND ', $and));
    }

    /**
     * @param DBRecord $record
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareDeleteStatement(DBRecord $record) {
        if (!($record instanceof User)) {
            throw new InvalidArgumentException("Object must be instance of User class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE ' . DBConfig::table(DBConfig::USERS) . ' SET deleted=1 WHERE id_u="' . (int) $record->getId() . '";');
        return $stmt;
    }

    /**
     * @param int $id
     * @return PDOStatement
     */
    protected function prepareFindStatement($id) {
        $conn = $this->getConnection();
        $stmt = $conn->prepare('SELECT * FROM test_users WHERE id_u = :i');
        $stmt->bindValue(':i', $id, PDO::PARAM_INT);
        return $stmt;
    }

    /**
     * @param DBRecord $record
     * @throws InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(DBRecord $record) {
        if (!($record instanceof User)) {
            throw new InvalidArgumentException("Object must be instance of User class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE ' . DBConfig::table(DBConfig::USERS) . ' SET
            login=:login, email=:email, activated=:status, firstname=:fn, surname=:sn
            WHERE id_u=:id;');
        $stmt->bindValue(':id', $record->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':login', $record->getLogin(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $record->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':status', $record->getStatus(), PDO::PARAM_INT);
        $stmt->bindValue(':fn', $record->getFirstname(), PDO::PARAM_STR);
        $stmt->bindValue(':sn', $record->getSurname(), PDO::PARAM_STR);
        return $stmt;
    }

    public function getTableHeader() {
        return array(
            'id' => '#',
            'login' => 'Login',
            'name' => 'Meno',
            'surname' => 'Priezvisko',
            'email' => 'Email',
            'dateRegistered' => 'Dátum registrácie',
            'dateLastActive' => 'Posledný prístup',
        );
    }

    public function getTableRowData($object = null) {
        if (!($object instanceof User)) {
            return array();
        }
        return array(
            'id' => $object->getId(),
            'login' => $object->getLogin(),
            'name' => $object->getFirstname(),
            'surname' => $object->getSurname(),
            'email' => $object->getEmail(),
            'dateRegistered' => $object->getDateRegistered('d.m.Y'),
            'dateLastActive' => $object->getLastActive('d.m.Y')
        );
    }

    public function getTableData(array $array) {
        $data = array();
        foreach ($array as $i) {
            $data[] = $this->getTableRowData($i);
        }
        return array(
            'header' => $this->getTableHeader(),
            'data' => $data
        );
    }

    public function getByLogin($login) {
        $db = $this->getConnection();
        $stmt = $db->prepare('SELECT * FROM test_users WHERE login=:login && deleted<>1 LIMIT 1;');
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $result = $this->customQuery($stmt);
        return $this->createFromResultSet($result[0]);
    }

    public function addGroup(User $user, Group $group) {
        $stmt = $this->getConnection()->prepare('INSERT INTO test_group_members (id_u, id_g, since) VALUES (:u, :g, :s);');
        $stmt->bindValue(':u', $user->getId());
        $stmt->bindValue(':g', $group->getId());
        $stmt->bindValue(':s', time());
        return $this->customQuery($stmt);
    }

    public function removeGroup(User $user, Group $group) {
        $stmt = $this->getConnection()->prepare('DELETE FROM test_group_members WHERE id_u=:u && id_g=:g');
        $stmt->bindValue(':u', $user->getId());
        $stmt->bindValue(':g', $group->getId());
        return $this->customQuery($stmt);
    }

}