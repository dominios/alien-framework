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
     * @return PDOStatement
     */
    protected function prepareCreateStatement() {
        // TODO: Implement prepareCreateStatement() method.
        throw new \RuntimeException("Unsupported operation.");
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
        // TODO: Implement prepareFindStatement() method.
        throw new \RuntimeException("Unsupported operation.");
    }

    /**
     * @param ActiveRecord $room
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(ActiveRecord $room) {
        // TODO: Implement prepareUpdateStatement() method.
        throw new \RuntimeException("Unsupported operation.");
    }
}