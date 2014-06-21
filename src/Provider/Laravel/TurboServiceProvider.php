<?php

/**
 * Think turbolinks but for your PHP application.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace Turbo\Provider\Laravel;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\DomCrawler\Crawler;
use Turbo\Turbo;

class TurboServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindIf('turbo.crawler', function () {
            return new Crawler;
        });

        $this->app->bindIf('turbo', function () {
            return new Turbo($this->app['turbo.crawler']);
        });

        $this->app->after(function ($request, $response) {
            // Dont handle redirects
            if ($response->isRedirection()) {
                return;
            }

            if (!$this->app['turbo']->isPjax()) {
                // Not a PJAX request
                return;
            }

            $html     = $response->getContent();
            $selector = $request->input('_pjax');
            $selector = $request->server('HTTP_X_PJAX_CONTAINER', $selector);

            $response->setContent($this->app['turbo']->extract($html, $selector));
        });
    }
}
