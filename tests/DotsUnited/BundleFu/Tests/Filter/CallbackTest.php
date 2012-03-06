<?php

/*
 * This file is part of BundleFu.
 *
 * (c) 2011 Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author  Jan Sorgalla <jan.sorgalla@dotsunited.de>
 * @version @package_version@
 */
class DotsUnited_BundleFu_Tests_CallbackTest extends PHPUnit_Framework_TestCase
{
    protected $called = false;
    public function testCallback()
    {
        $this->called = false;
        $callback = array($this, 'callback');

        $filter = new DotsUnited_BundleFu_Filter_Callback($callback);
        $result = $filter->filter('foo');

        $this->assertTrue($this->called);
        $this->assertEquals('bar', $result);
    }

    public function testFileCallback()
    {
        $called = false;
        $callbackFile = function() use(&$called) {
            $called = true;
            return 'bar';
        };

        $filter = new DotsUnited_BundleFu_Filter_Callback(null, $callbackFile);
        $result = $filter->filterFile('foo', '/js/js_1.js', new \SplFileInfo(__DIR__ . '/_files/js/js_1.js'));

        $this->assertTrue($called);
        $this->assertEquals('bar', $result);
    }

    public function testNullCallbacks()
    {
        $filter = new DotsUnited_BundleFu_Filter_Callback();

        $value = "foo";

        $this->assertEquals($value, $filter->filter($value));
        $this->assertEquals($value, $filter->filterFile($value, '/js/js_1.js', new \SplFileInfo(__DIR__ . '/_files/js/js_1.js')));
    }

    public function callback()
    {
        $this->called = true;
        return 'bar';
    }
}
