<?php

/**
 * Think turbolinks but for your PHP application.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace Turbo;

use Symfony\Component\DomCrawler\Crawler;
use InvalidArgumentException;

/**
 * Return the requested HTML for a PJAX request.
 */
class Turbo
{
    /**
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    protected $crawler;

    /**
     * Create a new instance of Turbo.
     *
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * Return crawler instance used to parse HTML.
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    public function getCrawler()
    {
        return $this->crawler;
    }

    /**
     * Set the crawler instance used to parse HTML.
     *
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     *
     * @return void
     */
    public function setCrawler(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * Is this request a PJAX request?
     *
     * @return bool
     */
    public function isPjax()
    {
        return (isset($_SERVER['HTTP_X_PJAX']) || isset($_GET['_pjax']));
    }

    /**
     * Extract title & HTML using the requested selector.
     *
     * @param string $content  HTML you want to parse.
     * @param string $selector A CSS selector.
     *
     * @return string
     */
    public function extract($content, $selector = 'body')
    {
        // Validate params
        if (!is_string($content) || !is_string($selector)) {
            throw new InvalidArgumentException('Invalid parameter. Only string supported');
        }

        $this->crawler->add($content);
        $selector = $this->crawler->filter($selector);

        // Did we find the container
        if ($selector->count() > 0) {
            // Grab the title
            $title = $this->crawler->filter('head > title');
            $title = ($title->count() > 0) ? sprintf('<title>%s</title>', $title->html()) : '';

            $content = $title.$selector->html();
        }

        return $content;
    }
}
