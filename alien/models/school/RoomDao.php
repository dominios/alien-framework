<?php

namespace Alien\Models\School;


use Alien\ActiveRecord;
use Alien\Db\CRUDDaoImpl;
use Alien\DBConfig;
use Alien\Models\Authorization\User;
use Alien\Models\Authorization\UserDao;
use InvalidArgumentException;
use PDO;
use PDOStatement;
use TableViewInterface;

class RoomDao extends CRUDDaoImpl implements TableViewInterface {

    protected $buildingDao;
    protected $userDao;

    public function __construct(PDO $connection, BuildingDao $buildingDao, UserDao $userDao) {
        parent::__construct($connection);
        $this->buildingDao = $buildingDao;
        $this->userDao = $userDao;
    }

    /**
     * @param Room $room
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareCreateStatement(Room $room = null) {
        if (!($room instanceof Room)) {
            throw new InvalidArgumentException("Building must be instance of Room class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('INSERT INTO ' . DBConfig::ROOMS . ' (building, responsible) VALUES (:b, :r);');
        $stmt->bindValue(':b', $room->getBuilding()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':r', $room->getResponsible()->getId(), PDO::PARAM_INT);
        return $stmt;
    }

    /**
     * @param array $result
     * @return ActiveRecord
     */
    protected function createFromResultSet(array $result) {
        $room = new Room();
        $room->setId($result['id']);
        $room->setBuilding($this->buildingDao->find($result['building']));
        $room->setResponsible($this->userDao->find($result['responsible']));
        $room->setFloor($result['floor']);
        $room->setNumber($result['number']);
        $room->setCapacity($result['capacity']);
        return $room;
    }

    /**
     * @return PDOStatement
     */
    protected function prepareSelectAllStatement() {
        $conn = $this->getConnection();
        return $conn->prepare('SELECT * FROM ' . DBConfig::ROOMS);
    }

    /**
     * @param ActiveRecord $record
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareDeleteStatement(ActiveRecord $record) {
        if (!($record instanceof Room)) {
            throw new InvalidArgumentException("Object must be instance of " . __NAMESPACE__ . " class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('DELETE FROM ' . DBConfig::ROOMS . ' WHERE id = "' . (int) $record->getId() . '";');
        return $stmt;
    }

    /**
     * @param int $id
     * @return mixed
     */
    protected function prepareFindStatement($id) {
        $conn = $this->getConnection();
        $stmt = $conn->prepare('SELECT * FROM ' . DBConfig::ROOMS . ' WHERE id = :i');
        $stmt->bindValue(':i', $id, PDO::PARAM_INT);
        return $stmt;
    }

    /**
     * @param ActiveRecord $room
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(ActiveRecord $room) {
        if (!($room instanceof Room)) {
            throw new InvalidArgumentException("Object must be instance of " . __NAMESPACE__ . " class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE ' . DBConfig::ROOMS . ' SET
            building=:b, responsible=:r, floor=:f, number=:n, capacity=:c
            WHERE id=:id;');
        $stmt->bindValue(':id', $room->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':b', $room->getBuilding()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':r', $room->getResponsible()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':f', $room->getFloor(), PDO::PARAM_INT);
        $stmt->bindValue(':n', $room->getNumber(), PDO::PARAM_STR);
        $stmt->bindValue(':c', $room->getCapacity(), PDO::PARAM_INT);
        return $stmt;
    }

    public function getTableHeader() {
        return array(
            'id' => '#',
            'building' => 'Budova',
            'floor' => 'Poshodie',
            'number' => 'Miestnosť',
            'capacity' => 'Kapacita',
            'responsible' => 'Zodpovedný'
        );
    }

    public function getTableRowData($object = null) {
        if (!($object instanceof Room)) {
            return array();
        }
        return array(
            'id' => $object->getId(),
            'building' => $object->getBuilding()->getName(),
            'floor' => $object->getFloor(),
            'number' => $object->getNumber(),
            'capacity' => $object->getCapacity(),
            'responsible' => $object->getResponsible()->getName()
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