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

            $turbo = new Turbo($response->getOriginalContent()->render());

            $response->setContent($turbo->getResponse());
        });
    }
}
