<?php

namespace Alien\Models\School;

use Alien\DBRecord;
use Alien\Db\CRUDDaoImpl;
use Alien\DBConfig;
use InvalidArgumentException;
use PDO;
use PDOStatement;
use TableViewInterface;

class BuildingDao extends CRUDDaoImpl implements TableViewInterface {

    /**
     * @param Building $building
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareCreateStatement(Building $building = null) {
        if (!($building instanceof Building)) {
            throw new InvalidArgumentException("Argument must be instance of Building class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('INSERT INTO `' . DBConfig::BUILDINGS . '`
            (`name`, `street`, `zip`, `city`, `state`) VALUES (:name, :street, :zip, :city, :state);');
        $stmt->bindValue(':name', $building->getName());
        $stmt->bindValue(':street', $building->getStreet());
        $stmt->bindValue(':zip', $building->getZip());
        $stmt->bindValue(':city', $building->getCity());
        $stmt->bindValue(':state', $building->getState());
        return $stmt;
    }

    /**
     * @param array $result
     * @return DBRecord
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
     * @param DBRecord $record
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareDeleteStatement(DBRecord $record) {
        if (!($record instanceof Building)) {
            throw new InvalidArgumentException("Object must be instance of " . __NAMESPACE__ . " class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('DELETE FROM ' . DBConfig::BUILDINGS . ' WHERE id = "' . (int) $record->getId() . '";');
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
     * @param DBRecord $record
     * @throws InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(DBRecord $record) {
        if (!($record instanceof Building)) {
            throw new InvalidArgumentException("Object must be instance of " . __NAMESPACE__ . " class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE ' . DBConfig::BUILDINGS . ' SET
            name=:name, street=:street, zip=:zip, city=:city, state=:state
            WHERE id=:id;');
        $stmt->bindValue(':id', $record->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':name', $record->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':street', $record->getStreet(), PDO::PARAM_STR);
        $stmt->bindValue(':city', $record->getCity(), PDO::PARAM_STR);
        $stmt->bindValue(':zip', $record->getZip(), PDO::PARAM_STR);
        $stmt->bindValue(':state', $record->getState(), PDO::PARAM_STR);
        return $stmt;
    }

    public function getTableHeader() {
        return array(
            'id' => '#',
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
            'id' => $object->getId(),
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