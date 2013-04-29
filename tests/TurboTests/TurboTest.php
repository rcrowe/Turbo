<?php

namespace TurboTests;

use PHPUnit_Framework_TestCase;
use Turbo\Turbo;

class TurboTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->turbo = new Turbo;
        $this->setIsPjax();
    }

    public function tearDown()
    {
        $this->removeIsPjax();
    }

    public function testIsPjax()
    {
        $this->assertTrue($this->turbo->isPjax());
        $this->removeIsPjax();
        $this->assertFalse($this->turbo->isPjax());

        // Test different variables that effect isPjax
        $_SERVER['HTTP_X_PJAX'] = true;
        $this->assertTrue($this->turbo->isPjax());
        $this->removeIsPjax();

        $_GET['_pjax'] = true;
        $this->assertTrue($this->turbo->isPjax());
        $this->removeIsPjax();
    }

    public function testNotString()
    {
        foreach (array(null, 1234, new \stdClass, true) as $check) {
            $this->assertEquals($check, $this->turbo->extract($check));
        }
    }

    public function testNoBodyFound()
    {
        foreach (array('test', '<body>test', 'test</body>') as $check) {
            $this->assertEquals($check, $this->turbo->extract($check));
        }
    }

    public function testBodyTagRemoved()
    {
        $str     = 'This is just a test';
        $content = $this->turbo->extract('<body>'.$str.'</body>');

        $this->assertEquals($str, $content);
    }

    public function testNoTitleFound()
    {
        // opening tag
        $content = $this->turbo->extract('<title>Noodle<body>Test</body>');
        $this->assertEquals('Test', $content);

        // closing tag
        $content = $this->turbo->extract('Noodle</title><body>Test</body>');
        $this->assertEquals('Test', $content);
    }

    public function testTitleFound()
    {
        $html  = '<html>';
        $html .= '<head><title>Hello world</title></head>';
        $html .= '<body>Butter Bean</body>';
        $html .= '</html>';

        $this->assertEquals('<title>Hello world</title>Butter Bean', $this->turbo->extract($html));
    }

    public function testIsNotPjaxExtract()
    {
        $this->removeIsPjax();
        $str = '<body>test</body>';

        $this->assertEquals($str, $this->turbo->extract($str));
    }

    public function setIsPjax()
    {
        $_SERVER['HTTP_X_PJAX'] = true;
        $_GET['_pjax'] = true;
    }

    public function removeIsPjax()
    {
        unset($_SERVER['HTTP_X_PJAX'], $_GET['_pjax']);
    }
}