<?php

namespace Alien\Mvc;

use Alien\Di\ServiceLocatorInterface;
use Alien\Mvc\Exception\NoResponseException;
use Alien\Mvc\Exception\NotFoundException;
use Alien\Routing;
use Alien\Routing\RequestInterface;
use Alien\Routing\RouteInterface;

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
     * Automatically injected instance of <i>ServiceLocator</i>
     * @var ServiceLocatorInterface
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
     */
    protected $actions;

    /**
     * @var RouteInterface
     */
    protected $route;

    /**
     * Automatically prepared View instance
     * @var View
     */
    protected $view = null;

    /**
     * Current HTTP request
     * @var RequestInterface
     */
    protected $request;

    /**
     * Automatically prepared response instance
     * @var Response
     */
    protected $response;

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

    public function __construct()
    {
        $this->clearQueue();
    }

    /**
     * Clears queue of actions
     */
    public function clearQueue()
    {
        $this->actions = [];
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
     * @param $route RouteInterface
     */
    public function setRoute(RouteInterface $route)
    {
        $this->route = $route;
    }

    /**
     * Returns request object
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets request
     * @param RequestInterface $request
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
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
     * Execute all actions in queue and return responses
     *
     * This method filters queue and ensure, that each action will be executed only once.
     * Each action name is then checked, if contains suffix <i>Action</i> and adds it if not.
     * During execution, <code>prepareView()</code> and <code>prepareResponse()</code> methods are called
     * to prepare instances of automatically available objects via <code>$this</code>.
     *
     * Each action should modify prepared <i>Response</i> (set it's content etc.) while returning nothing,
     * or return new instance instead.
     *
     * <b>WARNING:</b> if multiple actions are in queue, <i>View</i> and <i>Response</i> instances are re-created for each action execution.
     * @return Response[]
     */
    public function getResponses()
    {

        // call every action at most 1 time
        $this->actions = array_unique($this->actions);

        $responses = [];

        // if no actions are set, try to run default action
        if (!sizeof($this->actions)) {
            $this->actions[] = $this->defaultAction;
        }

        // checks if action name ends with postfix Action and adds it if not
        $this->actions = array_map(function ($action) {
            return preg_match('/(\w+)Action$/', $action) ? $action : $action . 'Action';
        }, $this->actions);

        // execute actions queue
        foreach ($this->actions as $action) {

            $this->view = $this->prepareView($action);

            if (!method_exists($this, $action)) {
                throw new NotFoundException("Action $action not found");
            }

            $response = $this->$action();
            if (is_null($response)) {
                array_push($responses, $this->getResponse());
            } else if ($response instanceof ResponseInterface) {
                array_push($responses, $response);
            } else {
                throw new NoResponseException("Response of action $action is empty");
            }

        }

        return $responses;
    }

    /**
     * Create automatically available View object
     * @param $action string action name
     * @return View
     */
    protected function prepareView($action)
    {
        $src = '';
        $src .= 'view/';
        $src .= strip_namespace(str_replace('Controller', '', get_called_class()));
        $src .= '/' . $action;
        $src .= '.php';
        return new View($src);
    }

    /**
     * Returns last prepared Response object if exists or prepare one
     * @return Response
     */
    public function getResponse()
    {
        if ($this->response === null) {
            $this->response = $this->prepareResponse();
        }
        return $this->response;
    }

    /**
     * Create automatically available Response object
     * @return Response
     */
    protected function prepareResponse()
    {
        return new Response(null, Response::STATUS_OK, 'text/plain;charset=UTF8');
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
        return in_array($action, $this->actions);
    }

    /**
     * Forces to execute given action.
     * All actions are removed from queue by calling this method. Execution continues with given action name.
     *
     * <b>NOTE:</b> any action inserted into queue <i>after</i> calling this method is executed as well.
     *
     * @param string $action action name to execute
     */
    public function forceAction($action)
    {
        $this->clearQueue();
        $this->addAction($action);
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
     * @return ServiceLocatorInterface
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

}
