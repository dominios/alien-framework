<?php

namespace Alien\Routing;
use InvalidArgumentException;

/**
 * Uri object encapsulation
 *
 * foo://username:password@example.com:8042/over/there/index.dtb?type=animal&name=narwhal#nose
 *   \_/   \_______________/ \_________/ \__/            \___/ \_/ \______________________/ \__/
 *    |           |               |       |                |    |            |                |
 *    |       userinfo           host    port              |    |          query          fragment
 *    |    \________________________________/\_____________|____|/ \__/        \__/
 *  scheme                 |                          |    |    |    |          |
 *   name              authority                      |    |    |    |          |
 *    |                                             path   |    |    interpretable as keys
 *    |                                                    |    |
 *    |    \_______________________________________________|____|/       \____/     \_____/
 *    |                         |                          |    |          |           |
 *  scheme              hierarchical part                  |    |    interpretable as values
 *   name                                                  |    |
 *    |            path               interpretable as filename |
 *    |   ___________|____________                              |
 *   / \ /                        \                             |
 *   urn:example:animal:ferret:nose               interpretable as extension
 *
 *                 path
 *          _________|________
 *  scheme /                  \
 *   name  userinfo  hostname       query
 *   _|__   ___|__   ____|____   _____|_____
 *  /    \ /      \ /         \ /           \
 *  mailto:username@example.com?subject=Topic
 *
 * @package Alien\Routing
 */
class Uri {

    /**
     * @var string
     */
    protected $protocol = "";

    /**
     * @var string
     */
    protected $username = "";

    /**
     * @var string
     */
    protected $password = "";

    /**
     * @var string
     */
    protected $host = "";

    /**
     * @var string
     */
    protected $port = "";

    /**
     * @var string
     */
    protected $path = "";

    /**
     * @var string
     */
    protected $query = "";

    /**
     * @var string
     */
    protected $fragment = "";

    /**
     * Factory method for creation from string
     * Method should be able to instantiate various given URIs: absolute, relative, ...
     *
     * Example of URI:<br>
     * <i>foo://username:password@example.com:8042/over/there/index.dtb?type=animal&name=narwhal#nose<i>
     *
     * @param string $string URI in string
     * @return Uri object
     * @throws InvalidArgumentException when given string is not valid URI
     */
    public static function createFromString($string) {
        $parts = parse_url($string);
        if($parts === false) {
            throw new InvalidArgumentException("String is not valid URI");
        }
        $uri = new self;
        if(array_key_exists('scheme', $parts)) {
            $uri->protocol = $parts['scheme'];
        }
        if(array_key_exists('user', $parts)) {
            $uri->username = $parts['user'];
        }
        if(array_key_exists('pass', $parts)) {
            $uri->password = $parts['pass'];
        }
        if(array_key_exists('host', $parts)) {
            $uri->host = $parts['host'];
        }
        if(array_key_exists('port', $parts)) {
            $uri->port = $parts['port'];
        }
        if(array_key_exists('path', $parts)) {
            $uri->path = $parts['path'];
        }
        if(array_key_exists('query', $parts)) {
            $uri->query = $parts['query'];
        }
        if(array_key_exists('fragment', $parts)) {
            $uri->fragment = $parts['fragment'];
        }
        return $uri;
    }

    /**
     * Returns protocol used if set
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Sets protocol
     * @param string $protocol
     * @return Uri
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * Returns username if set
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets username
     * @param string $username
     * @return Uri
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Returns password if set
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets password
     * @param string $password
     * @return Uri
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Returns host if set
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets host
     * @param string $host
     * @return Uri
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Returns host of set
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets port
     * @param string $port
     * @return Uri
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Returns directory path if set
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets path
     * @param string $path
     * @return Uri
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Returns query parameters if set
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Sets query parameters as string
     * @param string $query
     * @return Uri
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Returns fragment if set
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Sets fragment
     * @param string $fragment
     * @return Uri
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
        return $this;
    }

    /**
     * Returns all domains as array
     * Top level domains is at the highest index
     * @return string[]
     */
    public function getDomains()
    {
        return strlen($this->host) ? explode('.', $this->host) : "";
    }

    /**
     * Returns key/value array of query (GET) parameters from URI
     * @return array
     */
    public function getParams()
    {
        if(strlen($this->query)) {
            if(strpos($this->query, '&') !== false) {
                $pairs = explode('&', $this->query);
                $ret = [];
                foreach ($pairs as $pair) {
                    $keyValue = explode('=', $pair);
                    $ret[$keyValue[0]] = $keyValue[1];
                }
                return $ret;
            } else {
                $keyValue = explode('=', $this->query);
                return [
                    (string) $keyValue[0] => $keyValue[1]
                ];
            }
        } else {
            return [];
        }
    }

}