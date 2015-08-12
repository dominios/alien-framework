<?php

namespace Alien\Db;

use Alien\DBConfig;
use DateTime;
use PDO;

/**
 * Class Connection
 *
 * Framework wrapper of PDO connection
 * @package Alien\Db
 */
class Connection {

    /**
     * @var PDO
     */
    private $PDO = null;

    /**
     * Creates new connection based on PDO framework
     *
     * Also <code>UTF8 names</code> and timezone based on current <code>DateTime</code> during initialization.
     * @param $host string
     * @param $username string
     * @param $password string
     * @param $database string
     */
    public function __construct($host, $username, $password, $database) {

        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->query('SET NAMES utf8');

        /* nastavenie timezone */
        $now = new DateTime();
        $mins = $now->getOffset() / 60;
        $sgn = ($mins < 0 ? -1 : 1);
        $mins = abs($mins);
        $hrs = floor($mins / 60);
        $mins -= $hrs * 60;
        $offset = sprintf('%+d:%02d', $hrs * $sgn, $mins);
        $pdo->exec('SET time_zone="' . $offset . '"');
        $this->PDO = $pdo;

        require_once 'DBConfig.php';
    }

    /**
     * Sets database tables prefix when DBConfig class is used
     * @param $prefix
     * @deprecated
     */
    public function setDbPrefix($prefix) {
        DBConfig::setDBPrefix($prefix);
    }

    /**
     * Returns PDO object
     * @return PDO
     */
    public function getPDO() {
        return $this->PDO;
    }

}