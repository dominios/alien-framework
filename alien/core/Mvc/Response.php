<?php

namespace Alien\Mvc;

/**
 * Represents response from controller's action call
 *
 * Response is defined by it's data. Those data are returned back to application and in most cases into layout.
 * Response may also include information about HTTP status code, which defaults to 200 (SUCCESS).
 *
 * @package Alien\Mvc
 */
class Response implements ResponseInterface
{

    const HTTP_SUCCESS = 200;
    const HTTP_NOT_FOUND = 404;
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * @var int HTTP status code
     */
    protected $status;

    /**
     * @var string HTTP Content-Type header information
     */
    protected $contentType;

    /**
     * @var mixed response content
     */
    protected $content;

    /**
     * @param mixed $content response content
     * @param int $status HTTP status code
     * @param string $contentType content type
     */
    public function __construct($content = null, $status = self::HTTP_SUCCESS, $contentType = 'text/plain;charset=UTF8')
    {
        $this->status = $status;
        $this->content = $content;
        $this->contentType = $contentType;
    }

    /**
     * Returns HTTP status code
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets HTTP status code
     * @param int $status
     * @return Response
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Returns response content
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets content
     * @param mixed $content
     * @return Response
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Returns content type
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Sets content type
     * @param string $contentType
     * @return Response
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }


}

