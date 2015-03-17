<?php

namespace Alien\Models\School;


use Alien\DBRecord;
use Alien\Models\Authorization\User;

class Room implements DBRecord {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Building
     */
    protected $building;

    /**
     * @var User
     */
    protected $responsible;

    /**
     * @var int
     */
    protected $floor;

    /**
     * @var string
     */
    protected $number;

    /**
     * @var int
     */
    protected $capacity;

    public function setBuilding($building) {
        $this->building = $building;
        return $this;
    }

    /**
     * @return \Alien\Models\School\Building
     */
    public function getBuilding() {
        return $this->building;
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

    public function setFloor($floor) {
        $this->floor = $floor;
        return $this;
    }

    /**
     * @return int
     */
    public function getFloor() {
        return $this->floor;
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

    public function setNumber($number) {
        $this->number = $number;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumber() {
        return $this->number;
    }

    public function setResponsible($responsible) {
        $this->responsible = $responsible;
        return $this;
    }

    /**
     * @return \Alien\Models\Authorization\User
     */
    public function getResponsible() {
        return $this->responsible;
    }

    public function getName() {
        return $this->getBuilding()->getName() . ', ' . $this->getFloor() . '. posch., ' . $this->getNumber();
    }
}

