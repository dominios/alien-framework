<?php

namespace Alien;

use Alien\RouterException;

class Router {

    private $routes;

    public final function __construct() {
        $this->routes = include 'routes.php';
    }

    public function findMatch($request) {

        $result = array(
            'route' => null,
            'namespace' => null,
            'controller' => null,
            'action' => null,
            'params' => null
        );

        $match = false;
        $parts = array_filter(explode('/', $request, 3));

        foreach ($this->routes as $route => $options) {
            if ($route == $parts[1]) {
                $match = true;
                $this->parseNode($parts[2], $options, $result);
            }
        }

        if (!$match) {
            throw new RouterException('Route not found.');
        } else {
            $params = $this->getQueryParams($request, $result);
            $result['params'] = $params;
        }

        if ($result['controller'] === null) {
            throw new RouterException('Controller not set.');
        }

        if ($result['action'] === null) {
//            throw new RouterException('Action not set.');
        }

        return $result;
    }

    private function parseNode($url, $node, &$result) {

        if (is_array($node)) {

            $result['route'] .= $node['route'];

            if (array_key_exists('namespace', $node)) {
                $result['namespace'] = $node['namespace'];
            }
            if (array_key_exists('controller', $node)) {
                $result['controller'] = $node['controller'];
            }
            if (array_key_exists('action', $node)) {
                $result['action'] = $node['action'];
            }
            if (array_key_exists('childRoutes', $node)) {
                $parts = array_filter(explode('/', $url, 2));
                if (array_key_exists($parts[0], $node['childRoutes'])) {
                    $this->parseNode($parts[1], $node['childRoutes'][$parts[0]], $result);
                }
            }
        }

        return $result;
    }

    private function getQueryParams($url, $route) {

        if (strpos($route['route'], '[') !== false) {
            $requiredPart = preg_replace('/\[.*$/', '', $route['route']);
            var_dump("hladam $requiredPart v $url ");
            if (strpos($url, $requiredPart) === false) {
                throw new RouterException("Route mismatch.");
            }
        } else {
            $requiredPart = $route['route'];
        }


        $optionals = array();
        if (preg_match('/(\[.+\])/', $route['route'], $optionals)) {
            unset($optionals[0]);
            array_walk($optionals, function (&$value, $key) {
                $value = preg_replace('/[\[\]]/', '', $value);
            });
        }

        $params = array();
        $i = 1;
        $parametrized = implode('', array($requiredPart) + $optionals);
        $parametrizedParts = explode('/', $parametrized);
        $regexParts = array();
        foreach ($parametrizedParts as $p) {
            if (trim($p)) {
                if (strpos($p, ':') !== false) {
                    $params[str_replace(':', '', $p)] = $i;
                    $regexParts[] = '(.*)';
                } else {
                    $regexParts[] = "($p)";
                }
                $i++;
            }
        }

        $regex = '/^\/' . implode('\/', $regexParts) . '$/';

        $paramsMatches = array();
        if (preg_match($regex, $url, $paramsMatches)) {
            foreach ($params as $key => $index) {
                if ($paramsMatches[$index] == "") {
                    throw new RouterException("Required argument not found.");
                }
                $params[$key] = $paramsMatches[$index];
            }
        } else {
            if (count($params)) {
                throw new RouterException("Required argument not found.");
            }
        }

        return $params;

    }

    //


    /**
     * @param $route
     * @throws RouterException
     * @deprecated
     */
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

    /**
     * @param array $route
     * @return bool
     * @throws RouterException
     * @deprecated
     */
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

    /**
     * @param $route
     * @throws RouterException
     * @deprecated
     */
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

    /**
     * @param $url
     * @return mixed
     * @deprecated
     */
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

    /**
     * @param $query
     * @return mixed
     * @deprecated
     */
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