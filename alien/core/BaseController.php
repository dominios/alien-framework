<?php

namespace Alien\Controllers;

use Alien\Application;
use Alien\Terminal;
use Alien\Response;
use Alien\Models\Authorization\Authorization;
use Alien\Models\Authorization\User;
use Alien\Notification;
use Alien\Layout\Layout;
use Alien\Message;

class BaseController {

    protected $defaultAction = 'NOP';
    protected $actions;
    private $layout;
    private static $currentController;
    private static $instance = null;

    public final function __construct($args = null) {
//        Application::getInstance()->getConsole()->putMessage('Using <i>' . get_called_class() . '</i>');
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

    protected function init_action() {

//        Alien::getInstance()->getConsole()->putMessage('Called <i>AlienController::init_action()</i>.');

        self::$currentController = get_called_class();

        $auth = Authorization::getInstance();
        if (!$auth->getInstance()->isLoggedIn() && !in_array('login', $this->actions)) {
            unset($this->actions);
            $this->setLayout(new \Alien\Layout\LoginLayout());
            return;
        }

        $layout = new \Alien\Layout\IndexLayout();
        if ($layout::useNotifications) {
            $layout->setNotificationContainer(\Alien\NotificationContainer::getInstance());
        }
        $this->setLayout($layout);

        return new Response(array(
                'Title' => 'HOME',
                'LeftTitle' => Authorization::getCurrentUser()->getLogin(),
                'ContentLeft' => Array(Array('url' => BaseController::actionURL('', 'logout'), 'img' => 'logout.png', 'text' => 'Odhlásiť'))
            )
        );
    }

    private final function doActions() {

        $this->actions = array_unique($this->actions);

        $responses = Array();

        if (method_exists(get_called_class(), 'init_action')) {
            $response = $this->init_action();
            if ($response instanceof Response) {
                array_push($responses, $response);
            }
            Application::getInstance()->getConsole()->putMessage('Called <i>' . get_called_class() . '::init_action()</i>.');
        }

        if (!sizeof($this->actions)) {
            $this->actions[] = $this->defaultAction;
        }

        foreach ($this->actions as $action) {
            Application::getInstance()->getConsole()->putMessage('Calling action: <i>' . get_called_class() . '::' . $action . '</i>()');
            if (!method_exists($this, $action)) {
                Application::getInstance()->getConsole()->putMessage('Action <i>' . $action . '</i> doesn\'t exist!', Terminal::ERROR);
            }
            if (!method_exists($this, $action) && $action != $this->defaultAction) {
                $action = $this->defaultAction;
                $this->redirect($action);
                Application::getInstance()->getConsole()->putMessage('Calling action <i>' . get_called_class() . '::' . $action . '</i>() instead.');
            }
            $response = $this->$action();
            if ($response instanceof Response) {
                array_push($responses, $response);
            }
            Application::getInstance()->getConsole()->putMessage('Action <i>' . $action . '</i>() done.');
        }

        return $responses;
    }

    /**
     * @return Layout layout
     */
    public function getLayout() {
        return $this->layout;
    }

    public function setLayout(Layout $layout) {
        $this->layout = $layout;
    }

    protected function redirect($action, $statusCode = 301) {
        ob_clean();
        \Alien\NotificationContainer::getInstance()->updateSession();
        header('Location: ' . $action, false, $statusCode);
        ob_end_flush();
        exit;
    }

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
            }
        } else {
            unset($_GET);
            $_GET['id'] = $params[0];
        }


        $controller = __NAMESPACE__ . '\\' . ucfirst($controller) . 'Controller';

        return array(
            'controller' => $controller,
            'actions' => $actionsArray
        );
    }

    public static function actionURL($controller, $action, $params = null) {
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

    public static function getActionFromURL($actionURL, $includeQuery = false) {
        $actionURL = str_replace('http://' . $_SERVER['HTTP_HOST'], '', $actionURL);
        if (preg_match('/^\/alien/', $actionURL)) {
            $actionURL = str_replace('/alien/', '', $actionURL);
        }
        $words = explode('/', $actionURL);
        if ($includeQuery) {
            return implode('/', array_diff($words, array($words[0])));
        }
        return $words[1];
    }

    public static function getControllerFromURL($actionURL) {
        $actionURL = str_replace('http://' . $_SERVER['HTTP_HOST'], '', $actionURL);
        if (preg_match('/^\/alien/', $actionURL)) {
            $actionURL = str_replace('/alien/', '', $actionURL);
            $words = explode('/', $actionURL);
            return $words[0];
        }
    }

    public static function getRefererActionURL() {
        return BaseController::actionURL(BaseController::getControllerFromURL($_SERVER['HTTP_REFERER']), BaseController::getActionFromURL($_SERVER['HTTP_REFERER'], true));
    }

    public final function renderToString() {
        $responses = $this->doActions();
        foreach ($responses as $response) {
            $this->getLayout()->handleResponse($response);
//            AlienConsole::getInstance()->putMessage('Response from <i>'.$response->getAction().'</i> handled.');
        }
        $content = $this->layout->renderToString();
        return $content;
    }

    private function login() {
        if (isset($_POST['loginFormSubmit'])) {
            if (!Authorization::getInstance()->isLoggedIn()) {
                Authorization::getInstance()->login($_POST['login'], $_POST['pass']);
                $user = Authorization::getCurrentUser();
                if ($user instanceof User) {
                    if (Message::getUnreadCount($user)) {
                        Notification::newMessages("");
                    }
                }
            }
        }
        $this->redirect(BaseController::actionURL('dashboard', 'home'));
    }

    private function logout() {
        Authorization::getInstance()->logout();
        $this->redirect('/alien');
    }

    public static function getCurrentControllerClass() {
        return self::$currentController;
    }

    public static function isActionInActionList($action) {
        if (in_array($action, self::$instance->actions)) {
            return true;
        } else {
            return false;
        }
    }

    protected final function NOP() {
        return;
    }

    protected function error404() {

    }

    protected function error500() {

    }
}
