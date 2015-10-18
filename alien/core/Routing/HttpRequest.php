<?php

namespace Alien\Routing;

use Alien\Routing\Exception\InvalidHttpRequestException;

/**
 * Http requests object encapsulation
 *
 * @package Alien\Routing
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec5.html
 */
class HttpRequest implements RequestInterface
{

    const VERSION_10 = '1.0';
    const VERSION_11 = '1.1';
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_TRACE = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_OPTIONS = 'OPTIONS';

    /**
     * @var string[]
     */
    protected $headers;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var array
     */
    protected $params;

    /**
     * Factory method for <i>HttpRequest</i> creation from string
     *
     * Request-Line = Method SP Request-URI SP HTTP-Version CRLF<br>
     * Example of valid string:<br>
     * <i>GET http://www.example.com/index.html HTTP/1.1</i>
     *
     * @param $string string request line to parse
     * @return HttpRequest request object
     * @throws InvalidHttpRequestException when string does not contains all HTTP request parts
     * @throws InvalidHttpRequestException when not supported HTTP version is used
     * @throws InvalidHttpRequestException when not supported HTTP method is used
     * @throws InvalidHttpRequestException when URI is blank
     */
    public static function createFromString($string)
    {
        $parts = explode(" ", $string);
        if (count($parts) != 3) {
            throw new InvalidHttpRequestException("String does not contains all parts");
        }
        $method = trim($parts[0]);
        if (!in_array($method, [
            self::METHOD_GET,
            self::METHOD_CONNECT,
            self::METHOD_DELETE,
            self::METHOD_HEAD,
            self::METHOD_OPTIONS,
            self::METHOD_POST,
            self::METHOD_PUT,
            self::METHOD_TRACE
        ])
        ) {
            throw new InvalidHttpRequestException("Method $method is not supported HTTP method.");
        }

        $uri = trim($parts[1]);
        if (!strlen($uri)) {
            throw new InvalidHttpRequestException("URI cannot be empty.");
        }

        $version = explode('/', trim($parts[2]))[1];
        if (!in_array($version, [self::VERSION_10, self::VERSION_11])) {
            throw new InvalidHttpRequestException("Unsupported HTTP version $version.");
        }

        $request = new self;
        $request->setMethod($method);
        $request->setUri($uri);
        $request->setVersion($version);
        return $request;
    }

    /**
     * Factory method for <i>HttpRequest</i> creation from server
     *
     * This method uses superglobal array <code>$_SERVER</code> to build single
     * Request-Line consisting of <i>METHOD</i>, <i>URI</i> and <i>PROTOCOL</i>.
     * Headers and content is created via functions <code>getallheaders()</code>
     * and <code>file_get_contents()</code>.
     *
     * @return HttpRequest request object
     */
    public static function createFromServer()
    {
        $request = new self;
        $request->setMethod($_SERVER['REQUEST_METHOD']);
        $request->setUri($_SERVER['REQUEST_URI']);
        $request->setVersion($_SERVER['SERVER_PROTOCOL']);
        $request->setHeaders(getallheaders());
        $request->setContent(file_get_contents('php://input'));
        return $request;
    }

    /**
     * Returns HTTP request header(s)
     * When optional argument <code>$name</code> is used, returns specific header by name.
     * Otherwise, array of all headers is returned.
     *
     * <b>NOTE:</b> method may return <code>null</code> when asking for present header.
     *
     * @param string $name
     * @return string|\string[]
     */
    public function getHeaders($name = null)
    {
        return $name === null ? $this->headers : $this->headers[$name];
    }

    /**
     * Sets request headers
     * @param \string[] $headers
     * @return HttpRequest
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Returns HTTP request content
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets request content
     * @param string $content
     * @return HttpRequest
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Returns HTTP method used
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets request method
     * @param string $method
     * @return HttpRequest
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Returns URI
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Sets request URI
     * @param string $uri
     * @return HttpRequest
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Returns HTTP protocol version
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets request version
     * @param string $version
     * @return HttpRequest
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Checks if request method is HEAD
     * <b>NOTE:</b> this method is case-sensitive!
     * @return bool
     */
    public function isHead()
    {
        return $this->method === self::METHOD_HEAD;
    }

    /**
     * Checks if request method is GET
     * <b>NOTE:</b> this method is case-sensitive!
     * @return bool
     */
    public function isGet()
    {
        return $this->method === self::METHOD_GET;
    }

    /**
     * Checks if request method is POST
     * <b>NOTE:</b> this method is case-sensitive!
     * @return bool
     */
    public function isPost()
    {
        return $this->method === self::METHOD_POST;
    }

}