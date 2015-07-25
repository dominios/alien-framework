<?php

namespace Alien;

use Alien\Controllers\BaseController;
use Alien\Db\Connection;
use Alien\Di\ServiceManager;
use Alien\Models\Authorization\Authorization;
use Alien\Models\Authorization\User;
use BadFunctionCallException;
use Exception;
use PDO;
use RuntimeException;

final class Application {

    /**
     * @var Application
     */
    private static $instance;

    /**
     * @var bool
     */
    private static $boot = false;

    /**
     * @var array
     */
    private $config;

    /**
     * @var Terminal
     */
    private $console;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var Authorization
     */
    private $authorization;

    /**
     * @var Router
     */
    private $router;

    private function __construct() {
        $this->config = include 'config.php';
    }

    public static final function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Application;
        }
        return self::$instance;
    }

    /**
     * Initialize application. Can be run only once.
     *
     * @throws RuntimeException
     */
    public static function boot() {

        if (self::$boot === true) {
            throw new RuntimeException("Application can boot only once.");
        }

        self::$boot = true;
        $app = Application::getInstance();

        if($app->config['autoload']) {
            foreach($app->config['autoload'] as $dir) {
                $dh = \opendir($dir);
                if ($dh) {
                    while (false !== ($file = readdir($dh))) {
                        if (!is_dir($dir . '/' . $file)) {
                            include_once $dir . '/' . $file;;
                        }
                    }
                    closedir($dh);
                }
            }
        }

        date_default_timezone_set($app->config['timezone']);
        $app->console = Terminal::getInstance();

        $router = new Router();
        $app->router = $router;

        $sm = ServiceManager::initialize($app->config);
        $app->serviceManager = $sm;

        $connection = new Connection(array(
            'host' => $app->config['database']['host'],
            'database' => $app->config['database']['database'],
            'username' => $app->config['database']['user'],
            'password' => $app->config['database']['password'],
            'prefix' => $app->config['database']['prefix']
        ));

        $sm->registerService($connection->getPDO());

        $auth = Authorization::getInstance($sm);
        $app->authorization = $auth;
        $sm->registerService($auth);

    }

    /**
     * Runs application MVC and renders output into string
     *
     * @return string HTML output
     */
    public function run() {

        $request = str_replace('/alien', '', $_SERVER['REQUEST_URI']);

        $auth = $this->authorization;

        // @TODO toto prerobit nejako krajsie a nie hardcodovat
        if (!$auth->isLoggedIn()) {
            if (isset($_POST['loginFormSubmit'])) {
                $auth->login($_POST['login'], $_POST['pass']);
                $user = $auth->getCurrentUser();
                if ($user instanceof User) {
                    if (Message::getUnreadCount($user)) {
//                        Notification::newMessages("");
                        // $this->redirect(BaseController::staticActionURL('dashboard', 'home'));
//                        $route['controller'] = 'dashboard';
//                        $route['actions'] = array('home');
                    }
                }
            }
        }

        $controller = new BaseController();
        $content = '';

        try {

            if (!$auth->isLoggedIn()) {
                $route = $this->router->getRoute('login');
            } else {
                $route = $this->router->findMatch($request);
            }

            if (!$auth->isLoggedIn()) {
                $layout = new \Alien\Layout\LoginLayout();
                $route = $this->router->getRoute('login');
            } else {
                $layout = new \Alien\Layout\AdminLayout();
//            $layout = new \Alien\Layout\IndexLayout();
            }

            if ($layout::useNotifications) {
                $layout->setNotificationContainer(\Alien\NotificationContainer::getInstance());
            }

            // generovanie vystupu
            ob_clean(); // TODO zmazat odtialto!

            $className = '\\' . $route['namespace'] . '\\' . $route['controller'];

            if (class_exists($className)) {
                $controller = new $className();
            }

            $controller->setServiceManager($this->serviceManager);
            $controller->addAction($route['action']);
            $controller->setRoute($route);

            header('Content-Type: text/html; charset=utf-8'); // TODO typ podla response!

//            Notification::success("Vzorové úspešné hlásenie.");
//            Notification::information("Vzorové informatívne hlásenie.");
//            Notification::warning("Vzorové varovanie.");
//            Notification::error("Vzorová chyba.");

            $responses = $controller->getResponses();
            foreach ($responses as $response) {
                $layout->handleResponse($response);
            }
            $content .= $layout->__toString();

        } catch (FordbiddenException $e) {
            $controller->forceAction('error403', $e);
        } catch (RouterException $e) {
            $controller->forceAction('error404', $e);
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
