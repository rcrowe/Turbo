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
     * Create a new instance of Turbo.
     */
    public function __construct()
    {
    }

    /**
     * Is this request a PJAX request?
     */
    public function isPjax()
    {
        return (isset($_SERVER['HTTP_X_PJAX']) OR isset($_GET['_pjax']));
    }

    /**
     * If PJAX request, extract HTML body. Any problems we just sent back the original content.
     *
     * @param string $content Original content you want to extract.
     * @return string
     */
    public function extract($content)
    {
        // Send back the original content if we aren't supposed to be extracting
        if (!is_string($content) OR !$this->isPjax()) {
            return $content;
        }

        // We only process if we find a valid <body>
        preg_match('/(?:<body[^>]*>)(.*)<\/body>/isU', $content, $matches);

        // Did we find the body
        if (count($matches) !== 2) {
            return $content;
        }

        $body = $matches[1];

        // Does the page have a title
        preg_match('@<title>([^<]+)</title>@', $content, $matches);

        // Did we find the title
        $title = (count($matches) === 2) ? $matches[0] : '';

        // Set new content
        return $title.$body;
    }
}