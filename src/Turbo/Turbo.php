<?php

/**
 * Think turbolinks but for your PHP application.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace Turbo;

/**
 * Returns the body of the HTML along with any title.
 *
 * Checks for a PJAX request and tailors the response.
 */
class Turbo
{
    /**
     * @var string HTML response.
     */
    protected $response;

    /**
     * Create a new instance.
     *
     * @param string $response HTML.
     */
    public function __construct($response)
    {
        $this->response = $response;

        if (is_string($response) AND $this->isPjax()) {
            $this->process();
        }
    }

    /**
     * Get the response (May or may not have been processed).
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Is this request being made by PJAX.
     *
     * @return bool
     */
    public function isPjax()
    {
        return (isset($_SERVER['HTTP_X_PJAX']) OR isset($_GET['_pjax']));
    }

    /**
     * If PJAX request, extract HTML body.
     *
     * @return void
     */
    protected function process()
    {
        // We only process if we find a valid <body>
        preg_match('/(?:<body[^>]*>)(.*)<\/body>/isU', $this->response, $matches);

        // Did we find the body
        if (count($matches) !== 2) {
            return false;
        }

        $body = $matches[1];

        // Does the page have a title
        preg_match('@<title>([^<]+)</title>@', $this->response, $matches);

        // Did we find the title
        $title = (count($matches) === 2) ? $matches[0] : '';

        // Set the new response
        $this->response = $title.$body;
    }
}