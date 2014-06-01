<?php

namespace Alien;

use Alien\Controllers\BaseController;
use Alien\Db\Connection;
use Alien\Models\Authorization\UserDao;
use Alien\Models\Content\TemplateDao;
use BadFunctionCallException;
use Exception;
use PDO;
use RuntimeException;

final class Application {

    private static $instance;
    private static $boot = false;
    private $config;
    private $console;
    private $serviceManager;

    private function __construct() {
        $this->config = parse_ini_file('config.ini');
    }

    public static final function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Application;
        }
        return self::$instance;
    }

    /**
     * Initialize applicatin. Can be run only once.
     *
     * @throws RuntimeException
     */
    public static function boot() {
        if (self::$boot === true) {
            throw new RuntimeException("Application can boot only once.");
        }
        self::$boot = true;
        $app = Application::getInstance();
        date_default_timezone_set($app->config['timezone']);
        $app->console = Terminal::getInstance();

        $sm = ServiceManager::initialize($app->config);
        $app->serviceManager = $sm;

        $connection = new Connection(array(
            'host' => $app->config['dbHost'],
            'database' => $app->config['dbDatabase'],
            'username' => $app->config['dbUsername'],
            'password' => $app->config['dbPassword'],
            'prefix' => $app->config['dbPrefix']
        ));

        $sm->registerService($connection->getPDO());

        $userDao = new UserDao($connection->getPDO());
        $templateDao = new TemplateDao($connection->getPDO());
        $sm->registerService($userDao);
        $sm->registerService($templateDao);

    }

    /**
     * Runs application MVC and renders output into string
     *
     * @return string HTML output
     */
    public function run() {
        ob_clean();
        header('Content-Type: text/html; charset=utf-8');
        $content = '';
        try {
            $request = BaseController::parseRequest();
            if (class_exists($request['controller'])) {
                $controller = new $request['controller']($request['actions']);
            } else {
                $controller = new BaseController($request['actions']);
            }
            $controller->setServiceManager($this->serviceManager);
            $responses = $controller->doActions();
            foreach ($responses as $response) {
                $controller->getLayout()->handleResponse($response);
            }
            $content .= $controller->getLayout()->__toString();
        } catch (BadFunctionCallException $e) {
            $controller->forceAction('error404', $e);
        } catch (Exception $e) {
            $controller->forceAction('error500', $e);
        }

        return $content;
    }

    /**
     * @return Terminal
     * @deprecated
     */
    public function getConsole() {
        return $this->console;
    }

    /**
     * Gets database connection service object from ServiceManager
     *
     * @return PDO database connection
     * @deprecated use ServiceManager directly. This method will be removed in future!
     */
    public static final function getDatabaseHandler() {
        $app = Application::getInstance();
        return $app->getServiceManager()->getService('PDO');
    }

    /**
     * Returns system configuration value by key
     *
     * @param string $param key
     * @return mixed value
     * @deprecated
     */
    public static final function getParameter($param) {
        return self::getInstance()->config[$param];
    }

    /**
     * Returns initialized ServiceManager object
     *
     * @return ServiceManager
     */
    public function getServiceManager() {
        return $this->serviceManager;
    }
}
