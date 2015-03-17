<?php

namespace Alien\Models\School;


use Alien\DBRecord;

class Building implements DBRecord {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $street;

    /**
     * @var string
     */
    protected $zip;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $state;

    public function __construct() {

    }

    public function setCity($city) {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity() {
        return $this->city;
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

    public function setState($state) {
        $this->state = $state;
        return $this;
    }

    /**
     * @return string
     */
    public function getState() {
        return $this->state;
    }

    public function setStreet($street) {
        $this->street = $street;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreet() {
        return $this->street;
    }

    public function setZip($zip) {
        $this->zip = $zip;
        return $this;
    }

    /**
     * @return string
     */
    public function getZip() {
        return $this->zip;
    }

}