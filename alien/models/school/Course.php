<?php

namespace Alien\Models\School;


use Alien\Application;
use DateTime;

class Course {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Teacher
     */
    protected $teacher;

    /**
     * @var int
     */
    protected $capacity;

    /**
     * @var DateTime
     */
    protected $dateCreated;

    /**
     * @var DateTime
     */
    protected $dateStart;

    /**
     * @var DateTime
     */
    protected $dateEnd;

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

    public function setCapacity($capacity) {
        $this->capacity = $capacity;
        return $this;
    }

    /**
     * @return int
     */
    public function getCapacity() {
        return $this->capacity;
    }

    public function setDateCreated($dateCreated) {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated($format = null) {
        if ($format === null) {
            return $this->dateCreated;
        } else {
            return $this->dateCreated->format($format);
        }
    }

    public function setDateEnd($dateEnd) {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateEnd($format = null) {
        if ($format === null) {
            return $this->dateEnd;
        } else {
            return $this->dateEnd->format($format);
        }
    }

    public function setDateStart($dateStart) {
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateStart($format = null) {
        if ($format === null) {
            return $this->dateStart;
        } else {
            return $this->dateStart->format($format);
        }
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function setTeacher($teacher) {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return \Alien\Models\School\Teacher
     */
    public function getTeacher() {
        return $this->teacher;
    }


}