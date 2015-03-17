<?php

namespace Alien\Db;

use Alien\DBRecord;
use PDO;
use PDOException;
use PDOStatement;

abstract class CRUDDaoImpl implements CRUDDao {

    /**
     * Database connection object
     *
     * @var PDO
     */
    private $connection;

    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }

    /**
     * Get database connection object
     *
     * @return PDO
     */
    protected function getConnection() {
        return $this->connection;
    }

    /**
     * Executes prepared statement and returns fetched result
     *
     * @param PDOStatement $statement
     * @return array
     * @throws PDOException
     */
    private function execute(PDOStatement $statement) {
        if (!$statement->execute()) {
            throw new PDOException($statement->errorInfo());
        }
        if (strpos($statement->queryString, 'SELECT') !== false) {
            $result = $statement->fetchAll();
        } else {
            $result = true;
        }
        $statement->closeCursor();
        return $result;
    }

    /**
     * Saves new object into database
     *
     * @param DBRecord $object
     * @return void
     */
    public function create(DBRecord &$object) {
        $stmt = $this->prepareCreateStatement($object);
        $this->execute($stmt);
        $object->setId($this->getConnection()->lastInsertId());
    }

    /**
     * Delete record of object in database
     *
     * @param DBRecord $record
     */
    public function delete(DBRecord $record) {
        $stmt = $this->prepareDeleteStatement($record);
        $this->execute($stmt);
    }

    /**
     * @param DBRecord $record
     */
    public function update(DBRecord $record) {
        $stmt = $this->prepareUpdateStatement($record);
        $this->execute($stmt);
    }

    /**
     * Finds record by id and returns constructed object or false on failure
     *
     * @param int $id
     * @throws RecordNotFoundException
     * @return DBRecord|bool
     */
    public function find($id) {
        $stmt = $this->prepareFindStatement($id);
        $result = $this->execute($stmt);
        if (!count($result)) {
            throw new RecordNotFoundException("Record with id $id not found.");
        }
        $object = $this->createFromResultSet($result[0]);
        return $object;
    }

    /**
     * Returns flat array of all objects
     *
     * @return DBRecord[]
     */
    public function getList() {
        $stmt = $this->prepareSelectAllStatement();
        $result = $this->execute($stmt);
        $ret = array();
        foreach ($result as $row) {
            $ret[] = $this->createFromResultSet($row);
        }
        return $ret;
    }

    /**
     * Executes any prepared statement and returns fetched result
     *
     * @param PDOStatement $stmt
     * @return array
     */
    public function customQuery(PDOStatement $stmt) {
        return $this->execute($stmt);
    }

    /**
     * @return PDOStatement
     */
    protected abstract function prepareCreateStatement();

    /**
     * @param array $result
     * @return DBRecord
     */
    protected abstract function createFromResultSet(array $result);

    /**
     * @return PDOStatement
     */
    protected abstract function prepareSelectAllStatement();

    /**
     * @param DBRecord $record
     * @return PDOStatement
     */
    protected abstract function prepareDeleteStatement(DBRecord $record);

    /**
     * @param int $id
     * @return mixed
     */
    protected abstract function prepareFindStatement($id);

    /**
     * @param DBRecord $record
     * @return PDOStatement
     */
    protected abstract function prepareUpdateStatement(DBRecord $record);

}