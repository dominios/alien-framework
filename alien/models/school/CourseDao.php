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
use Alien\Models\Authorization\UserDao;
use DateTime;
use InvalidArgumentException;
use PDO;
use PDOStatement;
use TableViewInterface;

class CourseDao extends CRUDDaoImpl implements TableViewInterface {

    /**
     * @var UserDao
     */
    protected $userDao;

    public function __construct(PDO $connection, UserDao $userDao) {
        parent::__construct($connection);
        $this->userDao = $userDao;
    }


    /**
     * @param Teacher $teacher
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareCreateStatement(Teacher $teacher = null) {
        if (!($teacher instanceof Teacher)) {
            throw new \InvalidArgumentException("Argument must by instance of " . __NAMESPACE__ . "!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('INSERT INTO ' . DBConfig::table(DBConfig::COURSES) . ' (teacher) VALUES (:t);');
        $stmt->bindValue(':t', $teacher->getId(), PDO::PARAM_INT);
        return $stmt;
    }

    /**
     * @param array $result
     * @return ActiveRecord
     */
    protected function createFromResultSet(array $result) {

        $dc = new DateTime();
        $dc->setTimestamp($result['dateCreated']);

        $ds = new DateTime();
        $ds->setTimestamp($result['dateStart']);

        $de = new DateTime();
        $de->setTimestamp($result['dateEnd']);

        $course = new Course();
        $course->setId($result['id'])
               ->setName($result['name'])
               ->setCapacity($result['capacity'])
               ->setDateCreated($dc)
               ->setDateStart($ds)
               ->setDateEnd($de)
               ->setTeacher(new Teacher($this->userDao->find($result['teacher'])))
               ->setColor($result['color']);
        return $course;
    }

    /**
     * @return PDOStatement
     */
    protected function prepareSelectAllStatement() {
        $conn = $this->getConnection();
        return $conn->prepare('SELECT * FROM ' . DBConfig::COURSES);
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
        $stmt = $conn->prepare('DELETE FROM ' . DBConfig::table(DBConfig::COURSES) . ' WHERE id = "' . (int) $record->getId() . '";');
        return $stmt;
    }

    /**
     * @param int $id
     * @return PDOStatement
     */
    protected function prepareFindStatement($id) {
        $conn = $this->getConnection();
        $stmt = $conn->prepare('SELECT * FROM ' . DBConfig::COURSES . ' WHERE id = :i');
        $stmt->bindValue(':i', $id, PDO::PARAM_INT);
        return $stmt;
    }

    /**
     * @param ActiveRecord $room
     * @throws InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(ActiveRecord $room) {
        if (!($room instanceof Course)) {
            throw new InvalidArgumentException("Object must be instance of " . __NAMESPACE__ . " class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE ' . DBConfig::table(DBConfig::COURSES) . ' SET
            name=:n, teacher=:t, capacity=:c, dateCreated=:dc, dateStart=:ds, dateEnd=:de, color:cl
            WHERE id=:id;');
        $stmt->bindValue(':id', $room->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':n', $room->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':t', $room->getTeacher()->getId(), PDO::PARAM_STR);
        $stmt->bindValue(':c', $room->getCapacity(), PDO::PARAM_INT);
        $stmt->bindValue(':dc', $room->getDateCreated()->format("u"), PDO::PARAM_INT);
        $stmt->bindValue(':ds', $room->getDateStart()->format("u"), PDO::PARAM_STR);
        $stmt->bindValue(':de', $room->getDateEnd()->format("u"), PDO::PARAM_STR);
        $stmt->bindValue(':cl', $room->getColor(), PDO::PARAM_STR);
        return $stmt;
    }

    public function getTableHeader() {
        return array(
            'name' => 'Názov',
            'teacher' => 'Učiteľ',
            'capacity' => 'Kapacita',
            'dateStart' => 'Začiatok',
            'dateEnd' => 'Koniec'
        );
    }

    public function getTableRowData($object = null) {
        if (!($object instanceof Course)) {
            return array();
        }
        return array(
            'name' => $object->getName(),
            'teacher' => $object->getTeacher()->getName(),
            'capacity' => $object->getCapacity(),
            'dateStart' => $object->getDateStart('d.m.Y'),
            'dateEnd' => $object->getDateEnd('d.m.Y')
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