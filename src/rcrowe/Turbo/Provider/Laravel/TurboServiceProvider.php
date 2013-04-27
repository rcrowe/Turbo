<?php

namespace rcrowe\Turbo\Provider\Laravel;

use Illuminate\Support\ServiceProvider;
use rcrowe\Turbo\Turbo;

class TurboServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        \App::after(function($request, $response) {

            $turbo = new Turbo($response->getOriginalContent()->render());

            $response->setContent($turbo->getResponse());
        });
    }
}
