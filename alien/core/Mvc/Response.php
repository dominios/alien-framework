<?php

namespace Alien\Mvc;

/**
 * Represents response from controller's action call.
 *
 * Response is defined by it's data. Those data are returned back to application and in most cases into layout.
 * Response may also include information about HTTP status code, which defaults to 200 (SUCCESS).
 *
 * @package Alien\Mvc
 */
class Response implements ResponseInterface
{

    /**
     * MIME type for HTML documents.
     */
    const MIME_HTML = 'text/html;charset=UTF8';

    /**
     * MIME type for plaintext.
     */
    const MIME_TEXT = 'text/plain;charset=UTF8';

    /**
     * MIME type for JSON data.
     */
    const MIME_JSON = 'application/json;charset=UTF8';

    /**
     * MIME type for XML documents.
     */
    const MIME_XML = 'text/xml;charset=UTF8';

    /**
     * Response to a successful GET, PUT, PATCH or DELETE. Can also be used for a POST that doesn't result in a creation.
     */
    const STATUS_OK = 200;

    /*
     * Response to a POST that results in a creation. Should be combined with a Location header pointing to the location of the new resource.
     */
    const STATUS_CREATED = 201;

    /**
     * Response to a successful request that won't be returning a body (like a DELETE request).
     */
    const STATUS_NO_CONTENT = 204;

    /**
     * Used when HTTP caching headers are in play.
     */
    const STATUS_NOT_MODIFIED = 304;

    /**
     * The request is malformed, such as if the body does not parse.
     */
    const STATUS_BAD_REQUEST = 400;

    /**
     * When no or invalid authentication details are provided. Also useful to trigger an auth popup if the API is used from a browser.
     */
    const STATUS_UNAUTHORIZED = 401;

    /**
     * When authentication succeeded but authenticated user doesn't have access to the resource.
     */
    const STATUS_FORBIDDEN = 403;

    /**
     * When a non-existent resource is requested.
     */
    const STATUS_NOT_FOUND = 404;

    /**
     * When an HTTP method is being requested that isn't allowed for the authenticated user.
     */
    const STATUS_METHOD_NOT_ALLOWED = 405;

    /**
     * Indicates that the resource at this end point is no longer available. Useful as a blanket response for old API versions.
     */
    const STATUS_GONE = 410;

    /**
     * If incorrect content type was provided as part of the request.
     */
    const STATUS_UNSUPPORTED_MEDIA_TYPE = 415;

    /**
     *  Used for validation errors.
     */
    const STATUS_UNPROCESSABLE_ENTITY = 422;

    /**
     * When a request is rejected due to rate limiting.
     */
    const STATUS_TOO_MANY_REQUESTS = 429;

    /**
     * When server encountered an unexpected condition which prevented it from fulfilling the request.
     */
    const STATUS_INTERNAL_SERVER_ERROR = 500;

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
    public function __construct($content = null, $status = self::STATUS_OK, $contentType = 'text/plain;charset=UTF8')
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

