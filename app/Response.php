<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Response
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Response
{
    /**
     * Code
     *
     * @var int
     */
    protected $code;

    /**
     * Headers
     *
     * @var array
     */
    protected $headers = array();

    /**
     * Content
     *
     * @var string
     */
    protected $content;

    /**
     * Output response
     */
    public function output()
    {
        foreach ($this->headers as $header) {
            header($header, false);
        }
        if ($this->code) {
            http_response_code($this->code);
        }
        echo $this->content;
    }

    /**
     * Set page content
     *
     * @param string $content Data to set
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set header
     *
     * @param string $header Header
     */
    public function setHeader($header)
    {
        $this->headers[] = $header;
    }

    /**
     * Get headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set response code
     *
     * @param int $code Code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get response code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }
}
