<?php

namespace Alien\Models\School;

use Alien\DBRecord;
use Alien\Models\School\Course;
use Alien\Models\School\Room;
use DateTime;

class ScheduleEvent implements DBRecord {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Room
     */
    protected $room;

    /**
     * @var int
     */
    protected $year;

    /**
     * @var Course
     */
    protected $course;

    /**
     * @var DateTime
     */
    protected $dateFrom;

    /**
     * @var DateTime
     */
    protected $dateTo;

    public function setCourse($course) {
        $this->course = $course;
        return $this;
    }

    /**
     * @return Course
     */
    public function getCourse() {
        return $this->course;
    }

    public function setDateFrom(DateTime $dateFrom) {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateFrom($format = null) {
        return $format == null ? $this->dateFrom : $this->dateFrom->format($format);
    }

    public function setDateTo(DateTime $dateTo) {
        $this->dateTo = $dateTo;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTo($format = null) {
        return $format == null ? $this->dateTo : $this->dateTo->format($format);
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    public function setRoom($room) {
        $this->room = $room;
        return $this;
    }

    /**
     * @return Room
     */
    public function getRoom() {
        return $this->room;
    }

    public function setYear($year) {
        $this->year = $year;
        return $this;
    }

    /**
     * @return int
     */
    public function getYear() {
        return $this->year;
    }

} 