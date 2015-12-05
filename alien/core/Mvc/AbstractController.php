<?php

namespace Alien\Mvc;

use Alien\Di\ServiceLocatorInterface;
use Alien\Mvc\Exception\NoResponseException;
use Alien\Mvc\Exception\NotFoundException;
use Alien\Routing;
use Alien\Routing\HttpRequest;
use Alien\Routing\RequestInterface;
use Alien\Routing\Route;
use Alien\Routing\RouteInterface;
use InvalidArgumentException;

/**
 * Basic controller logic, parent of any controller defined in application
 *
 * Controller's actions are methods, that ends with <i>Action</i> suffix. Although it is not necessary,
 * these methods should not be visible from outside the class (e.g. private or protected).
 *
 * If trying to call undefined method, controller first tries to call it's default action, which is stored in protected property <code>$defaultAction</code>.
 * If calling of default action also fails, exception is thrown.
 *
 * With calling of action, instance of <i>\Alien\View</i> is created, used for rendering.
 *
 * Instance of <i>\Alien\Di\ServiceLocator</i> is injected into controller automatically.
 *
 * <b>USAGE</b>:
 *
 *  ...
 *
 * <b>WARNING:</b> each child controller should be named with postfix <i>Controller</i>, otherwise some of functionality may not work properly!
 *
 * @todo route should not be used to create actions list; use Request instead
 * @todo actions should not be only strings; make possible of Request
 * @todo define how default view path is defined
 * @todo check correct namespace for View
 * @todo do not auto create View - use autoload instead
 * @todo default View can be also XML/JSON - use Strategy pattern for it's creation
 * @package Alien\Controllers
 */
class AbstractController
{

    /**
     * Automatically injected DI container
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
     * @todo define exact types...
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
     * @var HttpRequest
     */
    protected $request;

    /**
     * Automatically prepared response instance
     * @var Response
     */
    protected $response;

    /**
     * @var array POST array
     * @deprecated
     * @todo WILL BE DELETED SOON
     */
    private $POST;

    /**
     * @var array GET array
     * @deprecated
     * @todo WILL BE DELETED SOON
     */
    private $GET;

    /**
     * Constructor
     */
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
     * Sets route
     * @param $route RouteInterface
     */
    public function setRoute(RouteInterface $route)
    {
        $this->route = $route;
    }

    /**
     * Returns request object
     * @return HttpRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets request
     * @param HttpRequest $request
     */
    public function setRequest(HttpRequest $request)
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
        $this->actions = array_unique($this->actions, SORT_REGULAR);

        $responses = [];

        // if no actions are set, try to run default action
        if (!sizeof($this->actions)) {
            $this->actions[] = $this->defaultAction;
        }
        
        // execute actions queue
        foreach ($this->actions as $action) {

            $args = [];
            $actionName = $action;
            if($action instanceof Route) {
                $actionName = $action->getAction();
                $args = array_values($action->getParams());
            }

            $this->view = $this->prepareView($actionName);

            $actionName = preg_match('/(\w+)Action$/', $actionName) ? $actionName : $actionName . 'Action';

            if (!method_exists($this, $actionName)) {
                throw new NotFoundException("Action $actionName not found");
            }

            $response = call_user_func_array([$this, $actionName], array_values($args));

            if (is_null($response)) {
                array_push($responses, $this->getResponse());
            } else if ($response instanceof ResponseInterface) {
                array_push($responses, $response);
            } else {
                throw new NoResponseException("Response of action $actionName is empty");
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
        $src .= \Alien\Stdlib\StringFunctions::stripNamespace(str_replace('Controller', '', get_called_class()));
        $src .= '/' . $action;
        $src .= '.php';
        return new View($src);
    }

    /**
     * Returns last prepared Response object if exists or prepare one
     * @return Response
     * @todo rename method or set as protected: currently, there is confusion betweeb getResponse() and getResponses() !
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
     * All actions are removed from queue by calling this method. Execution continues with given action.
     *
     * <b>NOTE:</b> any action inserted into queue <i>after</i> calling this method is executed as well.
     *
     * @param string $action action name to execute
     * @todo it's force action: this should also force executing this action
     */
    public function forceAction($action)
    {
        $this->clearQueue();
        $this->addAction($action);
    }

    /**
     * Append action to queue.
     *
     * By calling this method, <code>$action</code> is added to the end of actions queue.
     * Action can be either string or Request object. If string is given, it is considered
     * as action name without any arguments. When object is given, it must be instance of
     * <code>RequestInterface</code> to be able to extract needed information.
     *
     * @param $action string|RequestInterface
     * @throws InvalidArgumentException when unsupported type is passed as argument
     */
    public function addAction($action)
    {
        if (!is_string($action) && !($action instanceof Route)) {
            throw new InvalidArgumentException("Invalid action type given: " . gettype($action));
        }
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
     * @todo should be able to do it without dependency on $_SERVER
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
     * @todo use Route instance instead of string
     */
    protected function redirect($action, $statusCode = 301)
    {
        ob_clean();
        header('Location: ' . $action, false, $statusCode);
        ob_end_flush();
        exit;
    }

}
