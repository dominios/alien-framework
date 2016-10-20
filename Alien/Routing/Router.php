<?php

namespace Alien\Routing;

use Alien\Routing\Exception\InvalidConfigurationException;
use Alien\Routing\Exception\InvalidRequestException;
use Alien\Routing\Exception\RouteNotFoundException;
use InvalidArgumentException;

/**
 * Matching string requests with defined configurations
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
 * <li><b>defaults</b>: array with key/value pairs of default arguments values, if none given.</li>
 * </ul>
 *
 * When using <b>tree structure</b>, each <i>Route</i> may also
 * have defined multiple or none <i>child</i> routes.
 * In that case, child <i>route</i> has all of it's parent's routes patterns used as <b>prefix</b>, delimited with slash.
 * For example, route <i>"foo"</i> has child route <i>"boo"</i>. To match this URL, input must be <i>"/foo/bar"</i>.
 *
 * Child route automatically <b>inherits</b> all it's parent settings, but can freely overwrite it by defining new value.
 *
 * <b>WARNING:</b> empty route name and single slash are handled as equal!
 *
 * @todo remove namespace
 * @todo rename sub_routes
 * @todo HTTP method support
 * @todo empty child route (only variable) /:id
 * @todo rename to HttpRouter ?
 */
class Router
{

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
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Returns URL address of requested route
     * @param string $route
     * @return string
     */
    public static function getRouteUrl($route)
    {
        return $route;
    }

    public function match ($request) {
        $method = HttpRequest::METHOD_GET;
        if ($request instanceof HttpRequest) {
            $uri = $request->getUri();
            $method = $request->getMethod();
        } else if (is_string($request)) {
            $uri = $request;
        } else {
            throw new InvalidArgumentException("Invalid request given.");
        }

        // add slash at beginning of string if not present
        if (strpos($uri, '/') !== 0) {
            $uri = '/' . $uri;
        }

        $match = new RouteMatch();
        $hasMatch = $this->handler($this->routes, $uri, $match);

        if (!$hasMatch) {
            throw new RouteNotFoundException(sprintf("Route '%s' was not found", $uri));
        }
        return $match;
    }

    private function handler ($routes, $uri, RouteMatch $match) {
        $uriParts = array_filter(explode('/', $uri));
        $search = '/' . array_shift($uriParts);
        $hasMatch = false;

        foreach ($routes as $route) {

            $matches = [];
            if (preg_match('~' . $search . '(/:\w)*~' , $route['route'], $matches)) {

                $hasArguments = sizeof($matches) > 1;

                $match->apply($route);
                $hasMatch = true;

                if ($hasArguments) {

                    $arguments = $this->getPossibleArguments($route);
                    $required = $arguments['required'];
                    $optional = $arguments['optional'];
                    $match->applyParams(array_merge($required, $optional));

                    if (count($required) > $uriParts) {
                        // @todo add test coverage
                        throw new RouteNotFoundException("Not enough required arguments!");
                    } else {
                        $strippedPrefix = preg_replace("~^$search~", '', $route['route']);
                        $parts = array_values(array_filter(explode('/', $strippedPrefix)));
                        foreach ($parts as $index => $part) {
                            if (strpos($part, ':') !== false) {
                                // it is argument
                                $key = str_replace(':', '', $part);
                                $required[$key] = $uriParts[$index];
                            } else {
                                if ($uriParts[$index] !== $part) {
                                    throw new RouteNotFoundException("Path inside error");
                                }
                            }
                        }
                        $match->applyParams($required);
                        break;
                    }
                }

                // handle subroutes
                if (sizeof($uriParts)) {
                    if (array_key_exists('child_routes', $route)) {
                        // write test for situation when there are more uriParts but no child_routes => should be error
                        $subMatch = $this->handler($route['child_routes'], implode('/', $uriParts), $match);
                        if ($subMatch === false) {
                            $hasMatch = false;
                        }
                    }
                    else {
                        $subMatch = $this->handler([$route], implode('/', $uriParts), $match);
                        if ($subMatch === false) {
                            $hasMatch = false;
                        }
                    }
                }

                break;
            }
        }
        return $hasMatch ? $match : false;
    }

    private function getPossibleArguments ($route) {
        $ret = [
            'required' => [],
            'optional' => []
        ];
        $matches = [];
        preg_match_all('/(\:\w+)/', $route['route'], $matches);
        if (sizeof($matches)) {
            foreach ($matches[0] as $match) {
                $key = str_replace(':', '', $match);
                $ret['required'][$key] = null;
            }
        }
        return $ret;
    }

    /**
     * Search for route configuration
     *
     * Method accepts any string, which should be matched by router. Use slash as delimiter of tree-structure parts.
     * If match is found, associative array with following keys is returned:
     * <code>["route", "namespace", "controller", "action", "params", "defaults"]</code>
     *
     * @param string|HttpRequest $request URL or HttpRequest to find match for.
     * @return array configuration of found match.
     * @throws RouteNotFoundException when no match found in available routes.
     * @throws InvalidConfigurationException when configuration of matched route is invalid.
     * @throws InvalidArgumentException when given argument is other than HttpRequest or string.
     * @todo rename to something more self-explainable
     */
    public function getMatchedConfiguration($request)
    {

        if ($request instanceof HttpRequest) {
            $requestString = $request->getUri();
        } else if (is_string($request)) {
            $requestString = $request;
        } else {
            throw new InvalidArgumentException("Invalid request given.");
        }

        $result = array(
            'route' => null,
            'controller' => null,
            'action' => null,
            'params' => null,
            'defaults' => [],
        );

        // add slash at beginning of string if not present
        if (strpos($requestString, '/') !== 0) {
            $requestString = '/' . $requestString;
        }

        $match = false;
        // do not explode if only single slash "/" given
        if (strlen($requestString) === 1) {
            $parts[] = '';
        } else {
            $parts = array_values(array_filter(explode('/', $requestString, 3)));
        }

        foreach ($this->routes as $route => $options) {
            if ($route == $parts[0]) {
                $match = true;
                $this->parseNode(implode('/', $parts), $options, $result);
            }
        }

        if (!$match) {
            throw new RouteNotFoundException('Route not found.');
        } else {
            $params = $this->getQueryParams($requestString, $result);
            $result['params'] = $params;
        }

        if ($result['controller'] === null) {
            throw new InvalidConfigurationException('Controller not set.');
        }

        return $result;
    }

    /**
     * Parse single node and return configuration
     * Configuration contains route pattern, controller namespace and class name, action to call and parameters.
     *
     * <b>NOTE:</b> <code>$result</code> argument is passed by value due to sharing between multiple calls on different route configurations when parsing tree structure
     *
     * @param string $url parsing URL
     * @param array $node configuration of parsing route
     * @param array $result configuration of parsed route
     * @return array configuration of parsed route
     */
    private function parseNode($url, $node, &$result)
    {

        if (is_array($node)) {

            $result['route'] .= $node['route'];

            if (array_key_exists('controller', $node)) {
                $result['controller'] = $node['controller'];
            }
            if (array_key_exists('action', $node)) {
                $result['action'] = $node['action'];
            }
            if(array_key_exists('defaults', $node)) {
                $result['defaults'] = array_merge($result['defaults'], $node['defaults']);
            }
            if (array_key_exists('sub_routes', $node)) {
                $parts = array_values(array_filter(explode('/', $url)));
                if (count($parts) > 1) {
                    if (array_key_exists($parts[1], $node['sub_routes'])) {
                        $key = array_search($parts[1], $parts);
                        $rest = array_slice($parts, $key);
                        $this->parseNode(implode('/', $rest), $node['sub_routes'][$parts[1]], $result);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Checks route configuration and retrieve values for route parameters.
     * Not present optional parameters are replaced with <code>null</code> value.
     *
     * <b>NOTE</b>: if none argument is given, <code>defaults</code> part of configuration
     * is searched. If value is found, it is used as it was given in <i>Uri</i>.
     *
     * @param string $url requested URL
     * @param array $route route configuration
     * @return array key-value parameters
     * @throws InvalidConfigurationException when requested URL not matches given route configuration
     * @throws InvalidRequestException when invalid number of required parameters given
     * @throws InvalidRequestException when required argument is not found
     */
    private function getQueryParams($url, array $route)
    {

        // kontroluje ci sa v najdenej konfiguracii nachadza aj nepovinna cast
        // ak ano, kontroluje najprv pattern ako taky, ci je v poriadku (ci obsahuje povinne premenne)
        // vysledkom je $requiredPart - povinna zlozka URL
        if (strpos($route['route'], '[') !== false) {
            $requiredPart = preg_replace('/\[.*$/', '', $route['route']);
        } else {
            $requiredPart = $route['route'];
        }

        // zisti nepovinne argumenty a vrati ich pole
        $optionals = [];
        if (preg_match('/(\[\/\:[\w\d]+\])/', $route['route'], $optionals)) {
            $optionals = array_filter(array_unique($optionals));
            $optionals = array_map(function ($opt) {
                return preg_replace('/[\[\]]/', '', $opt);
            }, $optionals);
        }

        $params = [];
        $i = 1;
        $parametrized = implode('', array_merge([$requiredPart], $optionals));
        $parametrizedParts = array_filter(explode('/', $parametrized));

        $regexRequiredParts = [];
        $regexOptionalParts = [];

        foreach ($parametrizedParts as $part) {
            if (strpos($part, ':') !== false) {
                $params[str_replace(':', '', $part)] = $i;
                if(in_array("/$part", $optionals)) {
                    $regexOptionalParts[] = '(\/[\w\d]+)?';
                } else {
                    $regexRequiredParts[] = '([\w\d]+)';
                }
            } else {
                $regexRequiredParts[] = "($part)";
            }
            $i++;
        }

        $regex = '/^\/' . implode('\/', $regexRequiredParts) . implode('', $regexOptionalParts) . '$/';
        $paramsMatches = [];

        $optionals = array_map(function ($e) {
            return str_replace('/:', '', $e);
        }, $optionals);
        $defaults = array_key_exists('defaults', $route) ? $route['defaults'] : [];

        if (preg_match($regex, $url, $paramsMatches)) {

            foreach ($params as $key => $index) {

                $hasFromDefault = false;
                if(array_key_exists($key, $defaults)) {
                    $params[$key] = $defaults[$key];
                    $hasFromDefault = true;
                }

                if (!in_array($key, $optionals) && $paramsMatches[$index] == "" && $hasFromDefault) {
                    throw new InvalidRequestException("Required argument $key not found");
                }

                if(array_key_exists($index, $paramsMatches)) {
                    $params[$key] = str_replace('/', '', $paramsMatches[$index]);
                } else if (!$hasFromDefault) {
                    $params[$key ] = null;
                }

            }
        } else {
            if (count($params) && count($params) > count($optionals)) {
                throw new InvalidRequestException("Number of arguments mismatch");
            } elseif (count($optionals)) {
                $params = array_map(function ($p) {
                    return null;
                }, $params);
            }
        }

        return $params;
    }

    /**
     * Returns route configuration by it's name
     *
     * <b>NOTE</b>: This method is able to find match only at top level of tree (simple routes only).
     *
     * @param string $name
     * @return RouteInterface
     * @throws RouteNotFoundException when route is not found
     */
    public function getRoute($name)
    {
        if (array_key_exists($name, $this->routes)) {
            return Route::createFromRouteConfiguration($this->routes[$name]);
        } else {
            throw new RouteNotFoundException("Route not found");
        }
    }

}