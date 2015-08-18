<?php

namespace Alien\Routing;

use Alien\Routing\Exception\InvalidRequest;

/**
 * Class Uri
 * @package Alien\Routing
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
 */
class Uri {

    protected $protocol;
    protected $username;
    protected $password;
    protected $domains;
    protected $port;
    protected $path;
    protected $query;
    protected $fragment;

    public static function createFromString($string) {
        // foo://username:password@example.com:8042/over/there/index.dtb?type=animal&name=narwhal#nose
        $parts = parse_url($string);
        if($parts === false) {
            throw new \InvalidArgumentException("String is not valid URI.");
        }
        $uri = new self;
        $uri->protocol = $parts['scheme'];
        $uri->username = $parts['user'];
        $uri->password = $parts['pass'];
        $uri->domains = explode('.', $parts['host']);
        $uri->port = $parts['port'];
        $uri->path = $parts['path'];
        $uri->query = $parts['query'];
        $uri->fragment = $parts['fragment'];
        return $uri;
    }

}