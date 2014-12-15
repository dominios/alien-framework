<?php
/**
 * Created by PhpStorm.
 * User: Domino
 * Date: 1.11.2014
 * Time: 17:54
 */

namespace Alien\Models\School;


use Alien\ActiveRecord;
use Alien\Db\CRUDDaoImpl;
use Alien\DBConfig;
use DateTime;
use InvalidArgumentException;
use PDO;
use PDOStatement;

class ScheduleEventDao extends CRUDDaoImpl {

    /**
     * @var CourseDao
     */
    protected $courseDao;

    /**
     * @var RoomDao
     */
    protected $roomDao;

    public function __construct(PDO $connection, CourseDao $courseDao, RoomDao $roomDao) {
        parent::__construct($connection);
        $this->courseDao = $courseDao;
        $this->roomDao = $roomDao;
    }


    /**
     * @param ScheduleEvent $event
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareCreateStatement(ScheduleEvent $event = null) {
        if (!($event instanceof ScheduleEvent)) {
            throw new InvalidArgumentException("Object must be instance of Event class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('INSERT INTO ' . DBConfig::SCHEDULE . ' (room, year, course, dateFrom, dateTo) VALUES (:room, :year, :course, :dateFrom, :dateTo);');
        $stmt->bindValue(':room', $event->getRoom()->getId());
        $stmt->bindValue(':year', $event->getYear());
        $stmt->bindValue(':course', $event->getCourse()->getId());
        $stmt->bindValue(':dateFrom', $event->getDateFrom('U'));
        $stmt->bindValue(':dateTo', $event->getDateTo('U'));
        return $stmt;
    }

    /**
     * @param array $result
     * @return ActiveRecord
     */
    protected function createFromResultSet(array $result) {
        $event = new ScheduleEvent();
        $event->setId($result['id']);
        $event->setCourse($this->courseDao->find($result['course']));
        $event->setRoom($this->roomDao->find($result['room']));
        $df = new DateTime();
        $df->setTimestamp($result['dateFrom']);
        $dt = new DateTime();
        $dt->setTimestamp($result['dateTo']);
        $event->setDateFrom($df);
        $event->setDateTo($dt);
        return $event;
    }

    /**
     * @return PDOStatement
     */
    protected function prepareSelectAllStatement() {
        $conn = $this->getConnection();
        return $conn->prepare('SELECT * FROM ' . DBConfig::SCHEDULE);
    }

    /**
     * @param ActiveRecord $record
     * @return PDOStatement
     */
    protected function prepareDeleteStatement(ActiveRecord $record) {
        // TODO: Implement prepareDeleteStatement() method.
        throw new \RuntimeException("Unsupported operation.");
    }

    /**
     * @param int $id
     * @return mixed
     */
    protected function prepareFindStatement($id) {
        $conn = $this->getConnection();
        $stmt = $conn->prepare('SELECT * FROM ' . DBConfig::SCHEDULE . ' WHERE id = :i');
        $stmt->bindValue(':i', $id, PDO::PARAM_INT);
        return $stmt;
    }

    /**
     * @param \Alien\ActiveRecord $event
     * @throws \InvalidArgumentException
     * @internal param \Alien\ActiveRecord $room
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(ActiveRecord $event) {
        if (!($event instanceof ScheduleEvent)) {
            throw new InvalidArgumentException("Object must be instance of Event class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE ' . DBConfig::SCHEDULE . ' SET room=:room, year=:year, course=:course, dateFrom=:dateFrom, dateTo=:dateTo WHERE id=:id');
        $stmt->bindValue(':id', $event->getId());
        $stmt->bindValue(':room', $event->getRoom()->getId());
        $stmt->bindValue(':year', $event->getYear());
        $stmt->bindValue(':course', $event->getCourse()->getId());
        $stmt->bindValue(':dateFrom', $event->getDateFrom('U'));
        $stmt->bindValue(':dateTo', $event->getDateTo('U'));
        return $stmt;
    }
}