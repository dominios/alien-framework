<?php

namespace Alien\Models\School;

use Alien\DBRecord;
use Alien\Db\CRUDDaoImpl;
use Alien\DBConfig;
use Alien\Models\Authorization\UserDao;
use Alien\TableViewInterface;
use DateTime;
use InvalidArgumentException;
use PDO;
use PDOStatement;

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
    protected function prepareCreateStatement(Course $course = null) {
        if (!($course instanceof Course)) {
            throw new \InvalidArgumentException("Argument must by instance of Course !");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('INSERT INTO ' . DBConfig::COURSES . ' (teacher) VALUES (:t);');
        $stmt->bindValue(':t', $course->getTeacher()->getId(), PDO::PARAM_INT);
        return $stmt;
    }

    /**
     * @param array $result
     * @return DBRecord
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
               ->setTeacher(($this->userDao->find($result['teacher'])))
               ->setColor($result['color']);
        return $course;
    }

    /**
     * @return PDOStatement
     */
    protected function prepareSelectAllStatement() {
        $conn = $this->getConnection();
        return $conn->prepare('SELECT * FROM courses');
    }

    /**
     * @param DBRecord $record
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareDeleteStatement(DBRecord $record) {
        if (!($record instanceof Course)) {
            throw new InvalidArgumentException("Object must be instance of Course class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('DELETE FROM ' . DBConfig::COURSES . ' WHERE id = "' . (int) $record->getId() . '";');
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
     * @param DBRecord $course
     * @throws InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(DBRecord $course) {
        if (!($course instanceof Course)) {
            throw new InvalidArgumentException("Object must be instance of Course class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE courses SET
            name =:name, teacher =:teacher, capacity =:capacity, dateCreated =:dateCreated, dateStart =:dateStart, dateEnd =:dateEnd, color =:color
            WHERE id =:id;');
        $stmt->bindValue(':id', $course->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':name', $course->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':teacher', $course->getTeacher()->getId(), PDO::PARAM_STR);
        $stmt->bindValue(':capacity', $course->getCapacity(), PDO::PARAM_INT);
        $stmt->bindValue(':dateCreated', $course->getDateCreated()->format("U"), PDO::PARAM_INT);
        $stmt->bindValue(':dateStart', $course->getDateStart()->format("U"), PDO::PARAM_INT);
        $stmt->bindValue(':dateEnd', $course->getDateEnd()->format("U"), PDO::PARAM_INT);
        $stmt->bindValue(':color', $course->getColor(), PDO::PARAM_STR);
        return $stmt;
    }

    public function getTableHeader() {
        return array(
            'id' => '#',
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
            'id' => $object->getId(),
            'name' => $object->getName(),
            'teacher' => $object->getTeacher()->getFirstname() . ' ' . $object->getTeacher()->getSurname(),
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