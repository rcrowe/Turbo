<?php

/**
 * Think turbolinks but for your PHP application.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace Turbo\Provider\Laravel;

use Illuminate\Support\ServiceProvider;
use App;
use Event;
use Turbo\Turbo;

/**
 * Brings the power of Turbo/PJAX to Laravel.
 */
class TurboServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        App::after(function($request, $response) {

            $turbo = new Turbo;

            if ($turbo->isPjax()) {

                if (is_a($response, 'Illuminate\Http\Response')) {

                    // Extract the body from the response
                    $content = (string)$response->getOriginalContent();
                    $body    = $turbo->extract($content);

                    // Set new response content
                    $response->setContent($body);
                }

                // Fire that we are in a pjax request
                Event::fire('turbo.pjax', array($request, $response));
            }
        });
    }
}
