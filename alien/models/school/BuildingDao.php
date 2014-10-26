<?php
/**
 * Created by PhpStorm.
 * User: Domino
 * Date: 26.10.2014
 * Time: 14:00
 */

namespace Alien\Models\School;


use Alien\ActiveRecord;
use Alien\Db\CRUDDaoImpl;
use Alien\DBConfig;
use InvalidArgumentException;
use PDO;
use PDOStatement;

class BuildingDao extends CRUDDaoImpl {

    /**
     * @param Teacher $teacher
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareCreateStatement() {
        $conn = $this->getConnection();
        $stmt = $conn->prepare('INSERT INTO ' . DBConfig::table(DBConfig::BUILDINGS) . ';');
        return $stmt;
    }

    /**
     * @param array $result
     * @return ActiveRecord
     */
    protected function createFromResultSet(array $result) {
        $course = new Building($result['id'], $result);
        return $course;
    }

    /**
     * @return PDOStatement
     */
    protected function prepareSelectAllStatement() {
        $conn = $this->getConnection();
        return $conn->prepare('SELECT * FROM ' . DBConfig::BUILDINGS);
    }

    /**
     * @param ActiveRecord $record
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareDeleteStatement(ActiveRecord $record) {
        if (!($record instanceof Course)) {
            throw new InvalidArgumentException("Object must be instance of " . __NAMESPACE__ . " class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('DELETE FROM ' . DBConfig::table(DBConfig::BUILDINGS) . ' WHERE id = "' . (int) $record->getId() . '";');
        return $stmt;
    }

    /**
     * @param int $id
     * @return PDOStatement
     */
    protected function prepareFindStatement($id) {
        $conn = $this->getConnection();
        $stmt = $conn->prepare('SELECT * FROM ' . DBConfig::BUILDINGS . ' WHERE id = :i');
        $stmt->bindValue(':i', $id, PDO::PARAM_INT);
        return $stmt;
    }

    /**
     * @param ActiveRecord $building
     * @throws InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(ActiveRecord $building) {
        if (!($building instanceof Building)) {
            throw new InvalidArgumentException("Object must be instance of " . __NAMESPACE__ . " class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE ' . DBConfig::table(DBConfig::BUILDINGS) . ' SET
            name=:n, street=:s, zip=:z, city=:c, state=:s
            WHERE id=:id;');
        $stmt->bindValue(':id', $building->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':n', $building->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':s', $building->getStreet(), PDO::PARAM_STR);
        $stmt->bindValue(':c', $building->getCity(), PDO::PARAM_STR);
        $stmt->bindValue(':z', $building->getZip(), PDO::PARAM_STR);
        $stmt->bindValue(':s', $building->getState(), PDO::PARAM_STR);
        return $stmt;
    }
}