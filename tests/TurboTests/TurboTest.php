<?php

namespace TurboTests;

use PHPUnit_Framework_TestCase;
use Turbo\Turbo;
use ReflectionMethod;

class TurboTest extends PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $str   = 'This is an example';
        $turbo = new Turbo($str);
        $this->assertEquals($str, $turbo->getResponse());
    }

    public function testIsPjax()
    {
        $turbo = new Turbo('test');
        $this->assertFalse($turbo->isPjax());

        $_SERVER['HTTP_X_PJAX'] = true;
        $this->assertTrue($turbo->isPjax());
        $this->removeIsPjax();

        $_GET['_pjax'] = true;
        $this->assertTrue($turbo->isPjax());
        $this->removeIsPjax();

        $this->setIsPjax();
        $this->assertTrue($turbo->isPjax());
        $this->removeIsPjax();
    }

    public function testNoBodyFound()
    {
        $this->setIsPjax();

        // no body
        $method = new ReflectionMethod('Turbo\Turbo', 'process');
        $method->setAccessible(true);

        $turbo   = new Turbo('test');
        $process = $method->invoke($turbo);

        $this->assertFalse($process);

        // opening tag
        $method = new ReflectionMethod('Turbo\Turbo', 'process');
        $method->setAccessible(true);

        $turbo   = new Turbo('<body>test');
        $process = $method->invoke($turbo);

        $this->assertFalse($process);

        // closing tag
        $method = new ReflectionMethod('Turbo\Turbo', 'process');
        $method->setAccessible(true);

        $turbo   = new Turbo('test</body>');
        $process = $method->invoke($turbo);

        $this->assertFalse($process);

        // Valid, no false returned
        $method = new ReflectionMethod('Turbo\Turbo', 'process');
        $method->setAccessible(true);

        $turbo   = new Turbo('test</body>');
        $process = $method->invoke($turbo);

        $this->assertTrue(!$process);
    }

    public function testBodyTagRemoved()
    {
        $this->setIsPjax();
        $str   = 'This is just a test';
        $turbo = new Turbo('<body>'.$str.'</body>');

        $response = $turbo->getResponse();
        $this->assertEquals($str, $response);
        $this->removeIsPjax();
    }

    public function testNoTitleFound()
    {
        // opening tag
        $this->setIsPjax();
        $turbo = new Turbo('<title>Noodle<body>Test</body>');
        $response = $turbo->getResponse();
        $this->assertEquals('Test', $response);

        // closing tag
        $this->setIsPjax();
        $turbo = new Turbo('Noodle</title><body>Test</body>');
        $response = $turbo->getResponse();
        $this->assertEquals('Test', $response);
    }

    public function testTitleFound()
    {
        $this->setIsPjax();
        $html  = '<html>';
        $html .= '<head><title>Hello world</title></head>';
        $html .= '<body>Butter Bean</body>';
        $html .= '</html>';

        $turbo = new Turbo($html);
        $response = $turbo->getResponse();

        $this->assertEquals('<title>Hello world</title>Butter Bean', $response);
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