<?php

namespace Alien;

use Alien\RouterException;

class Router {

    private $routes;

    public final function __construct() {
        $this->routes = include 'routes.php';
        print_r($this->routes);
    }

    public function findRoute($route) {
        $route = str_replace('/', '', $route);
        if (array_key_exists($route, $this->routes['routes'])) {
            $found = $this->routes['routes'][$route];
            $controller = $found['namespace'] . '\\' . $found['controller'];
            $action = $found['action'];
            $rt = array(
                'controller' => $controller,
                'action' => $action
            );
            $this->validateRoute($rt);
        } else {
            throw new RouterException('Route not found.');
        }
    }

    public function validateRoute(array $route) {
        if (!class_exists($route['controller'])) {
            throw new RouterException("Route controller $route[controller] does not exists.");
        }
        $rc = new \ReflectionClass($route['controller']);
        if (!$rc->hasMethod($route['action'])) {
            throw new RouterException("Route action $route[action] does not exists.");
        }
        return true;
    }

    public function handle($route) {
        try {
            $route = $this->urlPath($route);
            $this->findRoute($route);
        } catch (RouterException $e) {
            $default = $this->routes['routes']['default'];
            $prefix = $default['prefix'];
            if (mb_strlen($prefix)) {
                $route = preg_replace("/($prefix)/", "", $route);
            }

            $pattern = '^';

            $components = explode('/', $default['pattern']);

            $controllerKey = array_search('{controller}', $components);
            if ($controllerKey === false) {
                throw new RouterException('Invalid route format: controller not found.');
            } else {
                $controllerKey++;
                $pattern .= '/(\w+)';
            }
            $actionKey = array_search('{action}', $components);
            if ($actionKey === false) {
                throw new RouterException('Invalid route format: action not found.');
            } else {
                $actionKey++;
                $pattern .= '/(\w+)';
            }

            $queryKey = array_search('{query}', $components);
            if ($queryKey === false) {
                throw new RouterException('Invalid route format: query not found.');
            } else {
                $queryKey++;
                $pattern .= '/(.*)';
            }


            $pattern .= '$';

            $matches = array();
            $route = preg_replace('~^\/+~', '/', $route); // duplicitne / nahradi jednym lomitkom
            preg_match("~$pattern~", $route, $matches);
            var_dump($route, $pattern, $matches);
            $controller = $default['namespace'] . '\\' . str_replace('{name}', ucfirst($matches[$controllerKey]), $default['controller']);
            $action = str_replace('{name}', $matches[$actionKey], $default['action']);

            $rt = array(
                'controller' => $controller,
                'action' => $action,
                'query' => $this->parseQueryString($matches[$queryKey])
            );
            if ($default['query'] == false) {
                unset($rt['query']);
            }
            $this->validateRoute($rt);
            var_dump($rt);
        }
    }

    public function urlPath($url) {
        $components = parse_url($url);
        return $components['path'];
    }

    /**
     * @return array
     * @deprecated
     */
    private static function parseRequest() {
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

    private function parseQueryString($query) {
        $GET = array();
        $params = explode('/', preg_replace('/\?.*/', '', $query)); // vyhodi vsetko ?... cize "stary get"
        if (count($params) >= 2) {
            unset($_GET);
            for ($i = 0; $i < count($params); $i++) {
                $_GET[$params[$i++]] = $params[$i];
            }
        } else {
            unset($_GET);
            $_GET['id'] = $params[0];
        }
        return $_GET;
    }

}