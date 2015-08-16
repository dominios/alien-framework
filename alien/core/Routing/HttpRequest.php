<?php

namespace Alien\Routing;

class HttpRequest implements RequestInterface {

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

    public static function createFromString($string) {
        throw new \RuntimeException("Not implemented yet.");
    }

    public function getHeaders($name = null) {
        throw new \RuntimeException("Not implemented yet.");
    }

    public function getContent() {
        throw new \RuntimeException("Not implemented yet.");
    }

    public function getMethod() {
        throw new \RuntimeException("Not implemented yet.");
    }

    public function getUri() {
        throw new \RuntimeException("Not implemented yet.");
    }

    public function getVersion() {
        throw new \RuntimeException("Not implemented yet.");
    }

    public function getHead() {
        throw new \RuntimeException("Not implemented yet.");
    }

    public function isHead() {
        throw new \RuntimeException("Not implemented yet.");
    }

    public function getGet() {
        throw new \RuntimeException("Not implemented yet.");
    }

    public function isGet() {
        throw new \RuntimeException("Not implemented yet.");
    }

    public function getPost() {
        throw new \RuntimeException("Not implemented yet.");
    }

    public function isPost() {
        throw new \RuntimeException("Not implemented yet.");
    }

    /**
     * @param \string[] $headers
     * @return HttpRequest
     */
    public function setHeaders($headers) {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param string $content
     * @return HttpRequest
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    /**
     * @param string $method
     * @return HttpRequest
     */
    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }

    /**
     * @param string $version
     * @return HttpRequest
     */
    public function setVersion($version) {
        $this->version = $version;
        return $this;
    }

}