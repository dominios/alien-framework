<?php

namespace Alien\Routing;

use Alien\Routing\Exception\RouteNotFoundException;
use Alien\Routing\Exception\InvalidConfigurationException;

/**
 * Class Router
 *
 * Router consists of array-like routes configuration. This configuration can be:
 *
 * <ul>
 * <li><b>canonical</b>: route defines exactly one URL pattern,</li>
 * <li><b>tree</b>: route may define child routes,</li>
 * <li><b>combined</b>: which is combination of these two options.</li>
 * </ul>
 *
 * Each route in configuration has <b>name</b>, which is also used as <b>key</b> in routes array.
 * In case of multiple times defined <i>names</i>, <b>LIFO</b> strategy is used to find the match.
 *
 * Single <b>Route</b> definition consists of following fields:
 *
 * <ul>
 * <li><b>route</b>: URL pattern, should be same as <b>key</b> and should always start with slash (e.g. <i>"/example"</i>)</li>
 * <li><b>controller</b>: with exact controller class name to use for handling the route,</li>
 * <li><b>namespace</b>: with exact namespace of used controller,</li>
 * <li><b>action</b>: with exact method name to call in used controller.</li>
 * </ul>
 *
 * When using <b>tree structure</b>, each <i>Route</i> may also have defined multiple or none <i>child</i> routes.
 * In that case, child <i>route</i> has all of it's parent's routes patterns used as <b>prefix</b>, delimited with slash.
 * For example, route <i>"foo"</i> has child route <i>"boo"</i>. To match this URL, input must be <i>"/foo/bar"</i>.
 *
 * Child route automatically <b>inherits</b> all it's parent settings, but can freely overwrite it by defining new value.
 *
 * @package Alien
 */
class Router {

    /**
     * Array of available routes.
     *
     * Keys are names of routes (i.e. they must be unique or are overwritten), value is their array-like configuration
     *
     * @var array
     */
    private $routes;

    /**
     * @param array $routes array with configuration of routes
     */
    public final function __construct(array $routes) {
        $this->routes = $routes;
    }

    /**
     * Search for match in available routes
     *
     * @param array $request
     * @return array configuration of found match
     * @throws RouteNotFoundException when no match found in available routes
     * @throws InvalidConfigurationException when configuration of route is invalid
     */
    public function getMatch($request) {

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
            throw new RouteNotFoundException('Route not found.');
        } else {
            $params = $this->getQueryParams($request, $result);
            $result['params'] = $params;
        }

        if ($result['controller'] === null) {
            throw new InvalidConfigurationException('Controller not set.');
        }

        return $result;
    }

    /**
     * Parse single node and return result
     *
     * @param string $url
     * @param array $node
     * @param array $result
     * @return array
     */
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

    /**
     * Returns parameter's values defined at route configuration
     *
     * @param string $url
     * @param array $route
     * @return array
     * @throws RouterException
     */
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

        $regex = '/^\/' . implode('\/?', $regexParts) . '$/';

        $paramsMatches = array();

        $optionals = array_map(function ($e) {
            return str_replace('/:', '', $e);
        }, $optionals);

        if (preg_match($regex, $url, $paramsMatches)) {
            foreach ($params as $key => $index) {
                if ($paramsMatches[$index] == "" && !in_array($key, $optionals)) {
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

    /**
     * Returns route configuration by it's name
     *
     * @param string $name
     * @return array
     */
    public function getRoute($name) {
        return $this->routes[$name];
    }

    /**
     * Returns URL address of requested route
     * @param string $route
     * @return string
     */
    public static function getRouteUrl($route) {
        return '/alien/' . $route;
    }

}