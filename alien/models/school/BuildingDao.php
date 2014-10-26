<?php

namespace Alien\Models\School;

use Alien\ActiveRecord;
use Alien\Db\CRUDDaoImpl;
use Alien\DBConfig;
use InvalidArgumentException;
use PDO;
use PDOStatement;
use stdClass;
use TableViewInterface;

class BuildingDao extends CRUDDaoImpl implements TableViewInterface {

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
        $building = new Building();
        $building->setId($result['id']);
        $building->setName($result['name']);
        $building->setCity($result['city']);
        $building->setStreet($result['street']);
        $building->setState($result['state']);
        $building->setZip($result['zip']);
        return $building;
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
     * @param ActiveRecord $room
     * @throws InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(ActiveRecord $room) {
        if (!($room instanceof Building)) {
            throw new InvalidArgumentException("Object must be instance of " . __NAMESPACE__ . " class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE ' . DBConfig::table(DBConfig::BUILDINGS) . ' SET
            name=:n, street=:s, zip=:z, city=:c, state=:s
            WHERE id=:id;');
        $stmt->bindValue(':id', $room->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':n', $room->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':s', $room->getStreet(), PDO::PARAM_STR);
        $stmt->bindValue(':c', $room->getCity(), PDO::PARAM_STR);
        $stmt->bindValue(':z', $room->getZip(), PDO::PARAM_STR);
        $stmt->bindValue(':s', $room->getState(), PDO::PARAM_STR);
        return $stmt;
    }

    public function getTableHeader() {
        return array(
            'name' => 'Názov',
            'street' => 'Ulica',
            'city' => 'Mesto',
            'zip' => 'PSČ',
            'state' => 'Štát'
        );
    }

    public function getTableRowData($object = null) {
        if (!($object instanceof Building)) {
            return array();
        }
        return array(
            'name' => $object->getName(),
            'street' => $object->getStreet(),
            'city' => $object->getCity(),
            'zip' => $object->getZip(),
            'state' => $object->getState()
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