<?php

namespace Alien;

use Alien\Controllers\BaseController;
use Alien\Db\Connection;
use Alien\Di\ServiceLocator;
use Alien\Models\Authorization\Authorization;
use Alien\Models\Authorization\User;
use Alien\Routing\Router;
use BadFunctionCallException;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

class Application {

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    /**
     * @var Authorization
     */
    private $authorization;

    /**
     * @var Router
     */
    private $router;

    /**
     * Returns configuration object
     * @return Configuration
     */
    public function getConfiguration() {
        return $this->configuration;
    }

    /**
     * Sets configuration
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration) {
        $this->configuration = $configuration;
    }

    /**
     * Initialize application. Should be run only once.
     * @param Configuration $configuration
     */
    public function bootstrap(Configuration $configuration = null) {

        $this->configuration = $configuration;

        date_default_timezone_set($this->getConfiguration()->get('timezone'));

        if($this->getConfiguration()->get('autoload')) {
            foreach($this->getConfiguration()->get('autoload') as $dir) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );
                foreach ($files as $fileinfo) {
                    require_once $fileinfo->getBasename();
                }
            }
        }

        // @todo odstranit singleton!
        $sm = ServiceLocator::initialize($this->getConfiguration());
        $this->serviceLocator = $sm;
        $sm->registerService($configuration);

        $this->router = $sm->getService('Router');

        $connection = $sm->getService('Connection');

        // @todo neprehodit Connection resp. PDO do service locatora tiez?
        $sm->registerService($connection->getPDO());

    }

    /**
     * Runs application MVC and renders output into string
     *
     * @return string HTML output
     * @todo toto takto zostat nemoze, privela zavislosti
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

        // @todo fuj!
        $controller = new BaseController();
        $content = '';

        try {

            if (!$auth->isLoggedIn()) {
                $route = $this->router->getRoute('login');
            } else {
                $route = $this->router->getMatch($request);
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

            $controller->setServiceLocator($this->serviceLocator);
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
     * Returns initialized ServiceLocator object
     *
     * @return ServiceLocator
     */
    public function getServiceLocator() {
        return $this->serviceLocator;
    }

}
