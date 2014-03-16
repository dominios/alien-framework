<?php

namespace Alien;

use Alien\Controllers\BaseController;
use Alien\Layout\ErrorLayout;
use \PDO;
use \DateTime;

final class Application {

    private static $instance;
    private $DBH = null;
    private $systemSettings;
    private $console;

    private function __construct() {
        $this->loadConfig();
        $this->console = Terminal::getInstance();
        date_default_timezone_set($this->systemSettings['timezone']);
        $this->connectToDatabase($this->systemSettings['db_host'], $this->systemSettings['db_database'], $this->systemSettings['db_username'], $this->systemSettings['db_password']);
    }

    public static final function getInstance() {
        if (!self::$instance) {
            self::$instance = new Application;
        }
        return self::$instance;
    }

    public static function boot() {
        $app = self::getInstance();
    }

    public function run() {
    ob_clean();
        header('Content-Type: text/html; charset=utf-8');

        try {
            $request = BaseController::parseRequest();
            if (class_exists($request['controller'])) {
                $controller = new $request['controller']($request['actions']);
            } else {
                $controller = new BaseController($request['actions']);
            }

            $responses = $controller->doActions();
            foreach ($responses as $response) {
                $controller->getLayout()->handleResponse($response);
            }
            $content = $controller->getLayout()->__toString();
            return $content;

        } catch(\BadFunctionCallException $e){
            $controller->forceAction('error404', $e);
        } catch(\Exception $e){
            $controller->forceAction('error500', $e);
        }
    }


    /**
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
        try {
            # MySQL with PDO_MYSQL
            $DBH = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
            $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // ZMENIT POTOM LEN NA EXCEPTION
            $DBH->query('SET NAMES utf8');
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
        $this->systemSettings = parse_ini_file('config.ini');
    }

    /**
     * vrati hodnotu konfiguracneho parametra
     * @param string parameter
     * @return mixed hodnota
     */
    public static final function getParameter($param) {
        return self::getInstance()->systemSettings[$param];
    }

}
