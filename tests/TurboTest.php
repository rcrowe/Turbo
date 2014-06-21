<?php

namespace Turbo\Tests;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Symfony\Component\DomCrawler\Crawler;
use Turbo\Turbo;

class TurboTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testSetCrawler()
    {
        $crawler = m::mock('Symfony\Component\DomCrawler\Crawler');
        $crawler->shouldReceive('fooBar')->andReturn('bingBang');

        $turbo = new Turbo($crawler);
        $this->assertEquals('bingBang', $turbo->getCrawler()->fooBar());

        $crawler = m::mock('Symfony\Component\DomCrawler\Crawler');
        $crawler->shouldReceive('fooBar')->andReturn('baz');

        $turbo->setCrawler($crawler);

        $this->assertEquals('baz', $turbo->getCrawler()->fooBar());
    }

    public function testIsPjax()
    {
        $turbo = new Turbo(new Crawler);

        $this->assertFalse($turbo->isPjax());
        $_SERVER['HTTP_X_PJAX'] = true;
        $this->assertTrue($turbo->isPjax());
        unset($_SERVER['HTTP_X_PJAX']);

        $this->assertFalse($turbo->isPjax());
        $_GET['_pjax'] = true;
        $this->assertTrue($turbo->isPjax());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidContent()
    {
        $turbo = new Turbo(new Crawler);
        $turbo->extract(false);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidSelector()
    {
        $turbo = new Turbo(new Crawler);
        $turbo->extract('test', false);
    }

    public function testSelectorNotFound()
    {
        $selector = m::mock('Symfony\Component\DomCrawler\Crawler');
        $selector->shouldReceive('count')->andReturn(0);

        $crawler = m::mock('Symfony\Component\DomCrawler\Crawler');
        $crawler->shouldReceive('add')->once();
        $crawler->shouldReceive('filter')->andReturn($selector);

        $turbo = new Turbo($crawler);
        $html  = '<html><body>Foo Bar</body></html>';

        $this->assertEquals($html, $turbo->extract($html));
    }

    public function testNoTitle()
    {
        $selector = m::mock('Symfony\Component\DomCrawler\Crawler');
        $selector->shouldReceive('count')->andReturn(1);
        $selector->shouldReceive('html')->andReturn('foo bar');

        $title = m::mock('Symfony\Component\DomCrawler\Crawler');
        $title->shouldReceive('count')->andReturn(0);

        $crawler = m::mock('Symfony\Component\DomCrawler\Crawler');
        $crawler->shouldReceive('add')->once();
        $crawler->shouldReceive('filter')->with('body')->andReturn($selector);
        $crawler->shouldReceive('filter')->with('head > title')->andReturn($title);

        $turbo = new Turbo($crawler);
        $html  = '<html><body>Foo Bar</body></html>';

        $this->assertEquals('foo bar', $turbo->extract($html));
    }

    public function testWithTitle()
    {
        $selector = m::mock('Symfony\Component\DomCrawler\Crawler');
        $selector->shouldReceive('count')->andReturn(1);
        $selector->shouldReceive('html')->andReturn('foo bar');

        $title = m::mock('Symfony\Component\DomCrawler\Crawler');
        $title->shouldReceive('count')->andReturn(1);
        $title->shouldReceive('html')->andReturn('this is a title');

        $crawler = m::mock('Symfony\Component\DomCrawler\Crawler');
        $crawler->shouldReceive('add')->once();
        $crawler->shouldReceive('filter')->with('body')->andReturn($selector);
        $crawler->shouldReceive('filter')->with('head > title')->andReturn($title);

        $turbo = new Turbo($crawler);
        $html  = '<html><body>Foo Bar</body></html>';

        $this->assertEquals('<title>this is a title</title>foo bar', $turbo->extract($html));
    }

    public function testDefaultSelector()
    {
        $html  = '<html>';
        $html .= '<body>';
        $html .= '<span id="foo">hello</span> world';
        $html .= '</body>';
        $html .= '</html>';

        $turbo = new Turbo(new Crawler);

        $this->assertEquals('<span id="foo">hello</span> world', $turbo->extract($html));
    }

    public function testWithSelector()
    {
        $html  = '<html>';
        $html .= '<body>';
        $html .= '<span id="foo">hello</span> world';
        $html .= '</body>';
        $html .= '</html>';

        $turbo = new Turbo(new Crawler);

        $this->assertEquals('hello', $turbo->extract($html, '#foo'));
    }
}
