<?php

namespace Alien\Controllers;

use Alien\Application;
use Alien\Di\ServiceLocator;
use Alien\Layout\ErrorLayout;
use Alien\Routing;
use Alien\RouterException;
use Alien\Di;
use Alien\Terminal;
use Alien\Response;
use Alien\Models\Authorization\Authorization;
use Alien\Models\Authorization\User;
use Alien\Notification;
use Alien\Layout\Layout;
use Alien\Message;
use Alien\View;

class BaseController {

    /**
     * @var ServiceLocator
     */
    protected $serviceLocator;

    /**
     * @var string
     */
    protected $defaultAction = 'loginScreen';

    /**
     * @var array
     */
    protected $actions;

    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var string
     */
    private static $currentController;

    /**
     * @var array POST array
     */
    private $POST;

    /**
     * @var array GET array
     */
    private $GET;

    /**
     * @var BaseController
     */
    private static $instance = null;

    /**
     * @var array
     */
    protected $route;

    /**
     * @var View
     */
    protected $view = null;


    /**
     * Controller constructor
     *
     * @param array|null $args array of actions
     */
    public final function __construct($args = null) {
        if (is_array($args)) {
            $this->actions = $args;
        } else {
            if ($args === null) {
                $this->actions[] = $this->defaultAction;
            } else {
                $this->actions[] = $args;
            }
        }
        self::$instance = $this;
    }

    public function clearQueue() {
        $this->actions = array();
    }

    public function addAction($action) {
        $this->actions[] = $action;
    }

    public function setRoute($route) {
        $this->route = $route;
    }

    public function getParam($key) {
        return $this->route['params'][$key];
    }

    /**
     * Initialize the Controller
     */
    protected function initialize() {

        self::$currentController = get_called_class();

//        $auth = Authorization::getInstance();
//        if (!$auth->getInstance()->isLoggedIn() && !in_array('login', $this->actions)) {
//            unset($this->actions);
//            $this->setLayout(new \Alien\Layout\LoginLayout());
//            return;
//        }
//
//        $layout = new \Alien\Layout\AdminLayout();
//        if ($layout::useNotifications) {
//            $layout->setNotificationContainer(\Alien\NotificationContainer::getInstance());
//        }
//
//        $this->setLayout($layout);

    }

    /**
     * Run all actions
     * @return array
     * @throws RouterException
     */
    public final function getResponses() {

        $this->actions = array_unique($this->actions);

        $responses = Array();

        if (method_exists(get_called_class(), 'initialize')) {
            $response = $this->initialize();
            if ($response instanceof Response) {
                array_push($responses, $response);
            }
        }

        if (!sizeof($this->actions)) {
            $this->actions[] = $this->defaultAction;
        }

        foreach ($this->actions as $action) {

            $viewSrc = strip_namespace(str_replace('Controller', '', $this->getCurrentControllerClass())) . '/' . $action;
            $this->view = new View('display/' . strtolower($viewSrc . '.php'));

            if (!method_exists($this, $action)) {
                throw new RouterException();
            }
            if (!method_exists($this, $action) && $action != $this->defaultAction) {
                $action = $this->defaultAction;
                $this->redirect($action);
            }
            $response = $this->$action();
            if ($response instanceof Response) {
                array_push($responses, $response);
            }
        }

        return $responses;
    }

    /**
     * Gets layout
     *
     * @return Layout layout
     * @deprecated
     */
    public function getLayout() {
        return $this->layout;
    }

    /**
     * Sets layout
     *
     * @param Layout $layout
     * @deprecated
     */
    public function setLayout(Layout $layout) {
        $this->layout = $layout;
    }

    /**
     * Perform redirect
     *
     * @param string $action URL to redirect
     * @param int $statusCode HTTP status code
     */
    protected function redirect($action, $statusCode = 301) {
        ob_clean();
        \Alien\NotificationContainer::getInstance()->updateSession();
        header('Location: ' . $action, false, $statusCode);
        ob_end_flush();
        exit;
    }

    /**
     * Perform refresh
     */
    protected function refresh() {
        $this->redirect($_SERVER['REQUEST_URI']);
    }

    /**
     * Parse current URL request
     *
     * @return array
     */
    public static function parseRequest() {
        $actionsArray = array();
        # najprv POST
        if (@sizeof($_POST)) {
            $arr = explode('/', $_POST['action'], 2);
            $controller = $arr[0];
            $actionsArray[] = $arr[1];
        }

        $request = str_replace('/alien', '', $_SERVER['REQUEST_URI']);
        $keys = explode('/', $request, 4);
        // zacina sa / takze na indexe 0 je prazdny string
        // 1 - controller
        // 2 - akcia
        // 3 - zatial zvysok parametre (GET)
        if (empty($controller)) {
            $controller = $keys[1];
        }
        if ($keys[2] !== null) {
            $actionsArray[] = $keys[2];
        }
        $params = explode('/', preg_replace('/\?.*/', '', $keys[3])); // vyhodi vsetko ?... cize "stary get"

        if (count($params) >= 2) {
            unset($_GET);
            for ($i = 0; $i < count($params); $i++) {
                $_GET[$params[$i++]] = $params[$i];
//                $this->GET[$params[$i++]] = $params[$i];
            }
        } else {
            unset($_GET);
            $_GET['id'] = $params[0];
//            $this->GET['id'] = $params[0];
        }


        $controller = __NAMESPACE__ . '\\' . ucfirst($controller) . 'Controller';

        return array(
            'controller' => $controller,
            'actions' => $actionsArray
        );
    }

    /**
     * Gets action URL by given parameters
     *
     * @param string $controller controller class name
     * @param callable $action action name
     * @param null|array $params array of GET parameters
     * @return string URL
     */
    public static function staticActionURL($controller, $action, $params = null) {
        $url = '/';
        if (preg_match('/alien/', getcwd())) {
            $url .= 'alien/';
        }
        $url .= $controller . '/' . $action;
        if (isset($params) && count($params) == 1 && array_key_exists('id', $params)) {
            $url .= '/' . $params['id'];
        } else {
            if (is_array($params)) {
                foreach ($params as $k => $v) {
                    $url .= '/' . $k . '/' . $v;
                }
            }
        }
        return $url;
    }

    public function actionUrl($action, $params = null) {
        $controller = strtolower(str_replace('Controller', '', strip_namespace(BaseController::getCurrentControllerClass())));
        return BaseController::staticActionURL($controller, $action, $params);
    }

    /**
     * Gets action name from URL
     * @param string $url
     * @param bool $includeQuery
     * @return string
     */
    public static function getActionFromURL($url, $includeQuery = false) {
        $url = str_replace('http://' . $_SERVER['HTTP_HOST'], '', $url);
        if (preg_match('/^\/alien/', $url)) {
            $url = str_replace('/alien/', '', $url);
        }
        $words = explode('/', $url);
        if ($includeQuery) {
            return implode('/', array_diff($words, array($words[0])));
        }
        return $words[1];
    }

    /**
     * Gets controller class name from URL
     * @param $url
     * @return mixed
     */
    public static function getControllerFromURL($url) {
        $url = str_replace('http://' . $_SERVER['HTTP_HOST'], '', $url);
        if (preg_match('/^\/alien/', $url)) {
            $url = str_replace('/alien/', '', $url);
            $words = explode('/', $url);
            return $words[0];
        }
    }

    /**
     * Gets refering URL
     *
     * @return string
     */
    public static function getRefererActionURL() {
        return BaseController::staticActionURL(BaseController::getControllerFromURL($_SERVER['HTTP_REFERER']), BaseController::getActionFromURL($_SERVER['HTTP_REFERER'], true));
    }

    /**
     * Gets current controller class name
     *
     * @return string
     */
    public static function getCurrentControllerClass() {
        return self::$currentController;
    }

    /**
     * Checks, if given action is in the action list
     *
     * @param string $action
     * @return bool
     */
    public static function isActionInActionList($action) {
        if (in_array($action, self::$instance->actions)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Forces to execute given action. All other waiting actions are discared.
     *
     * @param callable $action action to execute
     * @param null|array $arg action arguments
     * @return mixed
     */
    public function forceAction($action, $arg = null) {
        unset($this->actions);
        return $this->$action($arg);
    }

    /**
     * Empty operation
     *
     * @throws \BadFunctionCallException
     * @deprecated
     */
    protected final function NOP() {
        throw new \BadFunctionCallException();
    }

    /**
     * Login screen action
     *
     * @deprecated
     */
    protected function loginScreen() {
    }

    protected function error403() {
        ob_clean();
        $this->setLayout(new ErrorLayout());
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbideen', true, 403);
        header('Content-Type: text/html; charset=utf-8');
        $response = new Response(array(
                'ContentMain' => '<h1>403 Forbidden</h1><pre>' . print_r($this->getServiceLocator()->getService('Authorization')->getCurrentUser(), true) . '</pre>',
            )
        );
        $this->getLayout()->handleResponse($response);
        echo $this->getLayout()->__toString();
        exit;
    }

    /**
     * HTTP 404: Not Found page
     */
    protected function error404() {
        ob_clean();
        $this->setLayout(new ErrorLayout());
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Page Not Found', true, 404);
        header('Content-Type: text/html; charset=utf-8');
        $response = new Response(array(
                'ContentMain' => '<h1>404 Page Not Found</h1>',
            )
        );
        $this->getLayout()->handleResponse($response);
        echo $this->getLayout()->__toString();
        exit;
    }

    /**
     * HTTP 500: Internal server error page
     *
     * @param mixed $arg
     */
    protected function error500($arg) {
        ob_clean();
        $this->setLayout(new ErrorLayout());
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        header('Content-Type: text/html; charset=utf-8');
        $partialView = new View('display/layouts/error/partial/500.php');
        $partialView->exception = $arg;
        $response = new Response(array(
                'Title' => '500 Internal Server Error',
                'ContentMain' => $partialView->renderToString()
            )
        );
        $this->getLayout()->handleResponse($response);
        echo $this->getLayout()->__toString();
        exit;
    }

    /**
     * Sets service manager
     *
     * @param ServiceLocator $serviceLocator
     * @return $this
     */
    public function setServiceLocator(ServiceLocator $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Gets service manager
     *
     * @return ServiceLocator
     */
    public function getServiceLocator() {
        return $this->serviceLocator;
    }

}