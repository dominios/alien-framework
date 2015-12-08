<?php

namespace Alien\Mvc;

use Alien\Di\ServiceLocatorAwareInterface;
use Alien\Di\ServiceLocatorInterface;
use Alien\Mvc\Exception\NoResponseException;
use Alien\Mvc\Exception\NotFoundException;
use Alien\Routing;
use Alien\Routing\HttpRequest;
use Alien\Routing\Route;
use Alien\Routing\RouteInterface;
use Alien\Stdlib\Exception\NullException;
use BadMethodCallException;
use InvalidArgumentException;

/**
 * Basic controller logic, parent of any controller defined in application.
 *
 * Controller's actions are methods ended by <i>Action</i> suffix. Although it is not necessary,
 * these methods should not be visible from outside the class (e.g. <i>private</i> or <i>protected</i>).
 *
 * When undefined action is called, controller first tries to execute it's default action, which is stored in <code>$defaultAction</code> property.
 * When even default action does not exists, execution ends by throw of <code>NotFoundException</code>.
 *
 * By calling of any action, instance of <code>View</code> is created, used for rendering.
 *
 * Instance of <i>\Alien\Di\ServiceLocator</i> is injected into controller automatically.
 *
 * <b>WARNING:</b> each child controller should be named with postfix <i>Controller</i>, otherwise some of functionality may not work properly!
 *
 * @todo default View can be also XML/JSON - use Strategy pattern for it's creation
 */
class AbstractController implements ServiceLocatorAwareInterface
{

    /**
     * Automatically injected DI container.
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Name of default action to call.
     * @var string
     */
    protected $defaultAction;

    /**
     * Queue of actions to execute (FIFO).
     * @var array
     */
    protected $actions;

    /**
     * Route associated with current request.
     * @var RouteInterface
     */
    protected $route;

    /**
     * Automatically prepared View instance. (lazy loaded)
     * @var View
     */
    private $view;

    /**
     * Name of action currently processed.
     * @var
     */
    private $currentActionName;

    /**
     * Current HTTP request.
     * @var HttpRequest
     */
    protected $request;

    /**
     * Automatically prepared response instance (lazy loaded).
     * @var Response
     */
    private $response;

    /**
     * Constructor.
     *
     * Constructor automatically initializes empty actions queue.
     */
    public function __construct()
    {
        $this->clearQueue();
    }

    /**
     * Provides access to lazy loaded View or Response objects.
     *
     * @param string $name <i>view</i> or <i>response</i>
     * @return Response|View
     * @throws BadMethodCallException when accessing other property then View or Response.
     */
    public function __get($name)
    {
        switch ($name) {
            case 'view':
                return $this->getView();
            case 'response':
                return $this->getResponse();
            default:
                throw new BadMethodCallException("Magic access can be used only for view or response.");
        }
    }

    /**
     * Clears queue of actions.
     */
    public function clearQueue()
    {
        $this->actions = [];
    }

    /**
     * Sets route.
     * @param RouteInterface $route route associated with current request.
     */
    public function setRoute(RouteInterface $route)
    {
        $this->route = $route;
    }

    /**
     * Returns request object.
     * @return HttpRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets request.
     * @param HttpRequest $request current request.
     */
    public function setRequest(HttpRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Returns value of parameter from route by it's key.
     * @param string $key name of query parameter
     * @return mixed
     * @throws NullException when route is not set or when parameter does not exists.
     */
    public function getParam($key)
    {
        if (!($this->route instanceof RouteInterface)) {
            throw new NullException("Route is not set.");
        }
        if (!array_key_exists($key, $this->route['params'])) {
            throw new NullException(sprintf("No parameter '%s' for route found.", $key));
        }
        return $this->route['params'][$key];
    }

    /**
     * Execute all actions in queue and return responses.
     *
     * This method filters queue and ensure, that each action will be executed just once.
     * Each action name is then checked, if contains suffix <i>Action</i> and adds it if not.
     * During execution, access to prepared <code>View</code> or <code>Response</code> is created via
     * <code>getView()</code> or <code>getResponse()</code>. These objects are lazy loaded when needed.
     *
     * Each action should modify prepared <code>>Response</code> (set it's content etc.) while returning nothing,
     * or return new instance instead. Otherwise, <code>NoResponseException</code> is thrown.
     *
     * <b>WARNING:</b> if multiple actions are in queue, <i>View</i> and <i>Response</i> instances are re-created for each action execution.
     *
     * @return Response[]
     * @throws NoResponseException when action provides no valid response object.
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
            if ($action instanceof Route) {
                $actionName = $action->getAction();
                $args = array_values($action->getParams());
            }

            $this->currentActionName = $actionName;

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
                throw new NoResponseException("Response of action $actionName is empty.");
            }

            // forget current view/response; new will be lazy-loaded
            $this->view = null;
            $this->response = null;

        }

        return $responses;
    }

    /**
     * Create automatically available View object.
     *
     * By default, path to the template is following:<br>
     * <code>view/[controllerName]/[actionName].php</code>.
     *
     * <b>NOTE:</b> Whole namespace information is stripped from controller name. Suffix <i>Controller</i> is also removed.
     *
     * @param string $action name of current action processed.
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
     * Returns last prepared View object if exists or create one.
     * @return View
     */
    protected function getView()
    {
        if ($this->view === null) {
            $this->view = $this->prepareView($this->currentActionName);
        }
        return $this->view;
    }

    /**
     * Returns last prepared Response object if exists or create one.
     *
     * <b>WARNING:</b> This method returns current response object, or response created by calling
     * <code>prepareResponse()</code> method. This response <b>is not</b> final response of controller
     * after execution of all actions. For this purpose, use <code>getResponses()</code> instead.
     *
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
     * Create automatically available Response object.
     *
     * By default, response is set to:
     * * empty content (<code>null</code>)
     * * status code set to <i>200 OK</i>
     * * content-type set to <i>text/html;charset=UTF8</i>.
     *
     * <b>TIP:</b> Override this method in each controller to avoid creating response object
     * manually in each action. Use <code>getResponse()</code> to access this prepared action instead.
     *
     * @return Response
     */
    protected function prepareResponse()
    {
        return new Response(null, Response::STATUS_OK, 'text/html;charset=UTF8');
    }

    /**
     * Checks if given action name is in queue.
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
     *
     * All actions are removed from queue by calling this method. Execution continues with given action.
     *
     * <b>NOTE:</b> any action inserted into queue <i>after</i> calling this method will be also executed.
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
     * Push action to queue.
     *
     * By calling this method, <code>$action</code> is added at the end of the controller's actions queue.
     * Action can be either <code>string</code> or <code>Route</code> object. If string is given, it is considered
     * as action name without any arguments. When object is given, it must be instance of
     * <code>Route</code> to be able to extract needed information. Otherwise, exception is thrown.
     *
     * @param string|Route $action name of action or route
     * @throws InvalidArgumentException when unsupported argument type is passed.
     */
    public function addAction($action)
    {
        if (!is_string($action) && !($action instanceof Route)) {
            throw new InvalidArgumentException(sprintf("Invalid action type given: %s.", gettype($action)));
        }
        $this->actions[] = $action;
    }

    /**
     * Push action to queue.
     *
     * This method is alias of <code>addAction()</code>.
     * @param string|Route $action name of action or route
     */
    public function pushAction($action)
    {
        $this->addAction($action);
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Perform refresh.
     *
     * <b>WARNING</b>: Refresh is done immediately! All output will be erased and no other code will be processed.
     */
    protected function refresh()
    {
        $this->redirect($_SERVER['REQUEST_URI']);
    }

    /**
     * Perform redirect operation.
     *
     * <b>NOTE:</b> Controllers tries to redirect to given location directly.
     *
     * <b>TIP:</b> Always try to redirect to absolute URL's or use beginning slash.
     *
     * <b>WARNING:</b> Redirect is done immediately! All output will be erased and no other code will be processed.
     *
     * @param string $location URL to redirect
     * @param int $statusCode HTTP status code
     */
    protected function redirect($location, $statusCode = 301)
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Location: ' . $location, false, $statusCode);
        flush();
        exit;
    }

}
