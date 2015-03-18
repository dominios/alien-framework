<?php

namespace Alien\Db;

use Alien\DBConfig;
use DateTime;
use PDO;
use PDOException;

class Connection {

    /**
     * @var PDO
     */
    private $PDO = null;

    public function __construct(array $config) {

        $host = $config['host'];
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];
        $prefix = $config['prefix'];

        try {
            # MySQL with PDO_MYSQL
            $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ZMENIT POTOM LEN NA EXCEPTION
            $pdo->query('SET NAMES utf8');
        } catch (PDOException $e) {
            header("HTTP/1.1 503 Service Unavailable");
            die('error 503 connect na databazu, prerobit na error hlasku!');
        }
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
        DBConfig::setDBPrefix($prefix);
    }

    /**
     * @return PDO
     */
    public function getPDO() {
        return $this->PDO;
    }

}