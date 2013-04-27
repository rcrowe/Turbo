<?php

namespace rcrowe\Turbo;

class Turbo
{
    protected $response;

    public function __construct($response)
    {
        $this->response = $response;

        if (is_string($response) AND $this->isPjax()) {
            $this->process();
        }
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function isPjax()
    {
        return (isset($_SERVER['HTTP_X_PJAX']) OR isset($_GET['_pjax']));
    }

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