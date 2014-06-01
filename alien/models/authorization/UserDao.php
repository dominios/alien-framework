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
     * @param ActiveRecord $record
     * @throws InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(ActiveRecord $record) {
        if (!($record instanceof User)) {
            throw new InvalidArgumentException("Object must be instance of User class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE ' . DBConfig::table(DBConfig::USERS) . ' SET
            login=:login, email=:email, activated=:status, ban=:ban, firstname=:fn, surname=:sn
            WHERE id_u=:id;');
        $stmt->bindValue(':id', $record->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':login', $record->getLogin(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $record->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':status', $record->getStatus(), PDO::PARAM_INT);
        $stmt->bindValue(':ban', $record->getBanDate(), PDO::PARAM_INT);
        $stmt->bindValue(':fn', $record->getFirstname(), PDO::PARAM_STR);
        $stmt->bindValue(':sn', $record->getSurname(), PDO::PARAM_STR);
        return $stmt;
    }
}