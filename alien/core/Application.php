<?php

namespace Alien;

use \PDO;
use \DateTime;

final class Application {

    public static $SystemImgUrl = '/alien/display/img/';
    private static $instance;
    private $DBH = null;
    private $system_settings;
    private $queryCounter = null;
    private $console;

    private function __construct() {
        $this->loadConfig();
        $this->console = Terminal::getInstance();
        date_default_timezone_set($this->system_settings['timezone']);
        $this->connectToDatabase($this->system_settings['db_host'], $this->system_settings['db_database'], $this->system_settings['db_username'], $this->system_settings['db_password']);
    }

    public static final function getInstance() {
        if (!self::$instance) {
            self::$instance = new Application;
        }
        return self::$instance;
    }

    public static function boot(){
        $app = self::getInstance();
    }

    /**
     *
     * @return Terminal
     * @deprecated
     */
    public function getConsole() {
        return $this->console;
    }

    /**
     * TODO : nejaky AlienPDO ktory bude vsetko logovat ...
     * @global PDO $DBH db handler
     * @global int $queryCounter pocet vykonanych dotazov
     * @param string $host host
     * @param string $database databaza
     * @param string $username meno
     * @param string $password heslo
     * @return PDO database handler
     */
    private final function connectToDatabase($host, $database, $username, $password) {
//        include 'class.alien.pdo.php';
        try {
            # MySQL with PDO_MYSQL
            $DBH = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
            $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // ZMENIT POTOM LEN NA EXCEPTION
            $DBH->query('SET NAMES utf8');
            $sql = $DBH->query('SHOW SESSION STATUS LIKE "Queries";')->fetch();
            $this->queryCounter = $sql['Value'];
        } catch (PDOException $e) {
            header("HTTP/1.1 503 Service Unavailable");
            die('error 503 connect na databazu, prerobit na error hlasku!');
//            include 'alien/error/Error500.html';
            exit;
        }
        /* nastavenie timezone */
        $now = new DateTime();
        $mins = $now->getOffset() / 60;
        $sgn = ($mins < 0 ? -1 : 1);
        $mins = abs($mins);
        $hrs = floor($mins / 60);
        $mins -= $hrs * 60;
        $offset = sprintf('%+d:%02d', $hrs * $sgn, $mins);
        $DBH->exec('SET time_zone="' . $offset . '"');
        $this->console->putMessage('Database handler initialized.');
        $this->DBH = $DBH;
    }

    /**
     * Získa spojenie s databázou
     * @return PDO database handler
     * @deprecated
     */
    public static final function getDatabaseHandler() {
        if (self::getInstance()->DBH === null) {
            $config = parse_ini_file('config.ini', TRUE);
            Application::getInstance()->connectToDatabase($config['MYSQL']['db_host'], $config['MYSQL']['db_database'], $config['MYSQL']['db_username'], $config['MYSQL']['db_password']);
        }

        require_once 'DBConfig.php';
        DBConfig::setDBPrefix(Application::getParameter('db_prefix'));

        return self::getInstance()->DBH;
    }

    /**
     * Získa prefix tabuliek
     * @return string prefix
     * @deprecated
     */
    public static final function getDBPrefix() {
        return self::getParameter('db_prefix');
    }

    /**
     * nacita konfigiracny subor
     */
    private final function loadConfig() {
        $this->system_settings = parse_ini_file('config.ini');
    }

    /**
     * vrati hodnotu konfiguracneho parametra
     * @param string parameter
     * @return mixed hodnota
     */
    public static final function getParameter($param) {
        return self::getInstance()->system_settings[$param];
    }

}
