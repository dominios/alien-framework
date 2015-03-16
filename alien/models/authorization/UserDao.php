<?php

namespace Alien\Models\Authorization;

use Alien\ActiveRecord;
use Alien\Db\CRUDDaoImpl;
use Alien\DBConfig;
use Alien\Models\Authorization\User;
use Alien\ServiceManager;
use DateTime;
use InvalidArgumentException;
use PDO;
use PDOStatement;
use TableViewInterface;

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
     * @return ActiveRecord
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
    protected function prepareSelectAllStatement() {
        $conn = $this->getConnection();
        return $conn->prepare('SELECT * FROM test_users WHERE deleted <> 1');
    }

    /**
     * @param ActiveRecord $record
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareDeleteStatement(ActiveRecord $record) {
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
     * @param ActiveRecord $room
     * @throws InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(ActiveRecord $room) {
        if (!($room instanceof User)) {
            throw new InvalidArgumentException("Object must be instance of User class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE ' . DBConfig::table(DBConfig::USERS) . ' SET
            login=:login, email=:email, activated=:status, firstname=:fn, surname=:sn
            WHERE id_u=:id;');
        $stmt->bindValue(':id', $room->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':login', $room->getLogin(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $room->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':status', $room->getStatus(), PDO::PARAM_INT);
        $stmt->bindValue(':fn', $room->getFirstname(), PDO::PARAM_STR);
        $stmt->bindValue(':sn', $room->getSurname(), PDO::PARAM_STR);
        return $stmt;
    }

    public function getTableHeader() {
        return array(
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
}