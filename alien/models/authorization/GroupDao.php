<?php

namespace Alien\Models\Authorization;


use Alien\ActiveRecord;
use Alien\Db\CRUDDaoImpl;
use Alien\DBConfig;
use DateTime;
use InvalidArgumentException;
use PDO;
use PDOStatement;
use TableViewInterface;

class GroupDao extends CRUDDaoImpl implements TableViewInterface {

    protected $userDao;

    public function __construct(PDO $connection, UserDao $userDao) {
        parent::__construct($connection);
        $this->userDao = $userDao;
    }

    /**
     * @param Group $group
     * @return PDOStatement
     * @throws \InvalidArgumentException
     */
    protected function prepareCreateStatement(Group $group = null) {
        if (!($group instanceof Group)) {
            throw new InvalidArgumentException("Argument must be instance of " . __NAMESPACE__ . " class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('INSERT INTO ' . DBConfig::table(DBConfig::GROUPS) . ';');
        return $stmt;
    }

    /**
     * @param array $result
     * @return ActiveRecord
     */
    protected function createFromResultSet(array $result) {
        $group = new Group();
        $group->setId($result['id']);
        $group->setName($result['name']);
        $group->setDescription($result['description']);
        $dc = new DateTime();
        $dc->setTimestamp($result['dateCreated']);
        $group->setDateCreated($dc);
        return $group;
    }

    /**
     * @return PDOStatement
     */
    protected function prepareSelectAllStatement() {
        $conn = $this->getConnection();
        return $conn->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::GROUPS));
    }

    /**
     * @param ActiveRecord $record
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareDeleteStatement(ActiveRecord $record) {
        if (!($record instanceof Group)) {
            throw new InvalidArgumentException("Argument must be instance of " . __NAMESPACE__ . " class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('DELETE FROM ' . DBConfig::table(DBConfig::GROUPS) . ' WHERE id = "' . (int) $record->getId() . '";');
        return $stmt;
    }

    /**
     * @param int $id
     * @return mixed
     */
    protected function prepareFindStatement($id) {
        $conn = $this->getConnection();
        $stmt = $conn->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::GROUPS) . ' WHERE id = :i');
        $stmt->bindValue(':i', $id, PDO::PARAM_INT);
        return $stmt;
    }

    /**
     * @param ActiveRecord $group
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(ActiveRecord $group) {
        if (!($group instanceof Group)) {
            throw new InvalidArgumentException("Argument must be instance of " . __NAMESPACE__ . " class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE ' . DBConfig::table(DBConfig::GROUPS) . ' SET
            name=:n, description=:d
            WHERE id=:id;');
        $stmt->bindValue(':id', $group->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':n', $group->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':d', $group->getDescription(), PDO::PARAM_STR);
        return $stmt;
    }

    public function getTableHeader() {
        return array(
            'id' => '#',
            'name' => 'Názov',
            'description' => 'Popis',
            'countMembers' => 'Počet členov',
        );
    }

    public function getTableRowData($object = null) {
        if (!($object instanceof Group)) {
            return array();
        }
        return array(
            'id' => $object->getId(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'countMembers' => count($object->getMembers()),
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
}