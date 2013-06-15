<?php

/**
 * Think turbolinks but for your PHP application.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace Turbo\Provider\Fuel;

use Event;
use Turbo\Turbo;

/**
 * Brings the power of Turbo/PJAX to Fuel.
 */
class Response extends \Fuel\Core\Response
{
    /**
     * Get the content/body to return to browser.
     *
     * If the request is a pjax one, only the body is returned.
     *
     * @param string $value
     * @return string
     */
    public function body($value = false)
    {
        $value AND $this->body = $value;

        // Deal with pjax request
        $turbo = new Turbo;

        if ($turbo->isPjax()) {
            $this->body = $turbo->extract((string)$this->body);
        }

        // Fire event, then remove so that not called multiple times
        Event::trigger('turbo.pjax');
        Event::unregister('turbo.pjax');

        return parent::body($this->body);
    }
}
