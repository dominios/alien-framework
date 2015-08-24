<?php

namespace Alien\Mvc;

use Alien\Di;
use Alien\Di\ServiceLocatorInterface;
use Alien\Routing;
use Alien\View;

/**
 * Basic controller logic, parent of any controller defined in application
 *
 * Controller's actions are methods, that ends with <i>Action</i> string. Those methods should not be visible from outside the class (e.g. private or protected).
 * If trying to call undefined method, controller first tries to call it's default action, which is stored in protected property <code>$defaultAction</code>.
 * If calling of default action also fails, exception is thrown.
 *
 * With calling of action, instance of <i>\Alien\View</i> is created, used for rendering.
 *
 * Instance of <i>\Alien\Di\ServiceLocator</i> is injected into controller automatically.
 *
 * <b>WARNING:</b> each child controller should be named with postfix <i>Controller</i>, otherwise some of functionality may not work properly!
 *
 * @todo dopisat s akou cestou tento view existuje
 * @todo namespace View sa nemeni?
 * @package Alien\Controllers
 */
class AbstractController
{

    /**
     * @var string
     * @deprecated
     */
    private static $currentController;
    /**
     * Automatically injected instance of <i>ServiceLocator</i>
     * @var Di\ServiceLocatorInterface
     */
    protected $serviceLocator;
    /**
     * Name of default action to call
     * @var string
     */
    protected $defaultAction;
    /**
     * Array of actions co execute
     * @var array
     * @deprecated
     */
    protected $actions;
    /**
     * @var array
     * @deprecated
     * @todo co to je? zamenit ked tak za Route
     */
    protected $route;
    /**
     * Automatically created View object
     * @var View
     */
    protected $view = null;
    /**
     * @var array POST array
     * @todo co s tym?
     */
    private $POST;
    /**
     * @var array GET array
     * @todo co s tym?
     */
    private $GET;

    /**
     * @param array|null $args array of actions
     * @todo argumenty odtialto dat prec
     */
    public function __construct($args = null)
    {
        if (is_array($args)) {
            $this->actions = $args;
        } else {
            if ($args === null) {
                $this->actions[] = $this->defaultAction;
            } else {
                $this->actions[] = $args;
            }
        }
    }

    /**
     * Gets refering URL
     *
     * @return string
     * @deprecated
     * @todo odstranit uplne
     */
    public static function getRefererActionURL()
    {
        return AbstractController::staticActionURL(AbstractController::getControllerFromURL($_SERVER['HTTP_REFERER']), AbstractController::getActionFromURL($_SERVER['HTTP_REFERER'], true));
    }

    /**
     * Gets action URL by given parameters
     *
     * @param string $controller controller class name
     * @param callable $action action name
     * @param null|array $params array of GET parameters
     * @return string URL
     * @deprecated
     * @todo vymenit za napr. makeUriFromRoute a nech argument je Route - pripadne, o toto nech sa stara Router
     */
    public static function staticActionURL($controller, $action, $params = null)
    {
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

    /**
     * Gets controller class name from URL
     * @param $url
     * @return mixed
     * @deprecated
     * @todo odstranit uplne
     */
    public static function getControllerFromURL($url)
    {
        $url = str_replace('http://' . $_SERVER['HTTP_HOST'], '', $url);
        if (preg_match('/^\/alien/', $url)) {
            $url = str_replace('/alien/', '', $url);
            $words = explode('/', $url);
            return $words[0];
        }
    }

    /**
     * Gets action name from URL
     * @param string $url
     * @param bool $includeQuery
     * @return string
     * @deprecated
     * @todo odstranit uplne
     */
    public static function getActionFromURL($url, $includeQuery = false)
    {
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
     * Sets route
     * @param $route
     * @deprecated
     * @todo wtf? zamenit ked tak za Route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * Returns value of parameter from route by it's key
     * @param $key string
     * @return mixed
     * @todo prehodnotit ci to bude takto
     */
    public function getParam($key)
    {
        return $this->route['params'][$key];
    }

    /**
     * Run all actions
     * @return array
     * @todo prepisat krajsie; odstranit dependencies; napisat poriadny doc comment
     */
    public final function getResponses()
    {

        // call every action at most 1 time
        $this->actions = array_unique($this->actions);

        $responses = [];

        // search for initialize() method and if found, execute it
        if (method_exists(get_called_class(), 'initialize')) {
            $response = $this->initialize();
            if ($response instanceof Response) {
                array_push($responses, $response);
            }
        }

        // if no actions are set, try to run default action
        if (!sizeof($this->actions)) {
            $this->actions[] = $this->defaultAction;
        }

        // execute actions queue
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
     * Initialize the Controller
     * @deprecated
     */
    protected function initialize()
    {
    }

    /**
     * Gets current controller class name
     *
     * @return string
     * @deprecated
     * @todo odstranit uplne
     */
    public static function getCurrentControllerClass()
    {
        return self::$currentController;
    }

    /**
     * Perform redirect operation
     *
     * @param string $action URL to redirect
     * @param int $statusCode HTTP status code
     * @deprecated
     * @todo akciu prerobit na Route ked tak
     * @todo Notifikacie odstranit ak su nastavene
     */
    protected function redirect($action, $statusCode = 301)
    {
        ob_clean();
        header('Location: ' . $action, false, $statusCode);
        ob_end_flush();
        exit;
    }

    /**
     * @param $action
     * @param null $params
     * @return string
     * @deprecated
     * @todo rovnako ako metoda vyssie
     */
    public function actionUrl($action, $params = null)
    {
        $controller = strtolower(str_replace('Controller', '', strip_namespace(AbstractController::getCurrentControllerClass())));
        return AbstractController::staticActionURL($controller, $action, $params);
    }

    /**
     * Checks if given action name is in queue
     *
     * @param string $action name
     * @return bool
     */
    public function isActionInActionList($action)
    {
        if (in_array($action, $this->actions)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Forces to execute given action.
     * All actions are removed from queue by calling this method. Execution continues with given action name.
     * <b>NOTE:</b> any action inserted into queue <i>after</i> calling this method is executed as well.
     *
     * @param string $action action name to execute
     * @return mixed
     */
    public function forceAction($action)
    {
        $this->clearQueue();
        $this->addAction($action);
    }

    /**
     * Clears queue of actions to execute
     */
    public function clearQueue()
    {
        $this->actions = [];
    }

    /**
     * Adds action into execution queue defined by it's name
     * @param $action string
     */
    public function addAction($action)
    {
        $this->actions[] = $action;
    }

    /**
     * Returns ServiceLocator instance
     * @return ServiceLocator
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Sets service manager
     * @param ServiceLocatorInterface $serviceLocator
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Perform refresh
     * @deprecated
     * @todo nastavit v zavislosti od predch. metody
     */
    protected function refresh()
    {
        $this->redirect($_SERVER['REQUEST_URI']);
    }

}