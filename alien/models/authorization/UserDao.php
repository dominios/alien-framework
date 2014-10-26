<?php

namespace Alien\Models\Authorization;

use Alien\ActiveRecord;
use Alien\Db\CRUDDaoImpl;
use Alien\DBConfig;
use Alien\Models\Authorization\User;
use InvalidArgumentException;
use PDO;
use PDOStatement;

class UserDao extends CRUDDaoImpl {

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
        $user = new User($result['id_u'], $result);
        return $user;
    }

    /**
     * @return PDOStatement
     */
    protected function prepareSelectAllStatement() {
        $conn = $this->getConnection();
        return $conn->prepare('SELECT * FROM test_users');
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
            login=:login, email=:email, activated=:status, ban=:ban, firstname=:fn, surname=:sn
            WHERE id_u=:id;');
        $stmt->bindValue(':id', $room->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':login', $room->getLogin(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $room->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':status', $room->getStatus(), PDO::PARAM_INT);
        $stmt->bindValue(':ban', $room->getBanDate(), PDO::PARAM_INT);
        $stmt->bindValue(':fn', $room->getFirstname(), PDO::PARAM_STR);
        $stmt->bindValue(':sn', $room->getSurname(), PDO::PARAM_STR);
        return $stmt;
    }
}