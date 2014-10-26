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

class CourseDao extends CRUDDaoImpl {

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
        $course = new Course($result['id'], $result);
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
     * @param ActiveRecord $building
     * @throws InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(ActiveRecord $building) {
        if (!($building instanceof Course)) {
            throw new InvalidArgumentException("Object must be instance of " . __NAMESPACE__ . " class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE ' . DBConfig::table(DBConfig::COURSES) . ' SET
            name=:n, teacher=:t, capacity=:c, dateCreated=:dc, dateStart=:ds, dateEnd=:de
            WHERE id=:id;');
        $stmt->bindValue(':id', $building->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':n', $building->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':t', $building->getTeacher()->getId(), PDO::PARAM_STR);
        $stmt->bindValue(':c', $building->getCapacity(), PDO::PARAM_INT);
        $stmt->bindValue(':dc', $building->getDateCreated()->format("u"), PDO::PARAM_INT);
        $stmt->bindValue(':ds', $building->getDateStart()->format("u"), PDO::PARAM_STR);
        $stmt->bindValue(':de', $building->getDateEnd()->format("u"), PDO::PARAM_STR);
        return $stmt;
    }
}