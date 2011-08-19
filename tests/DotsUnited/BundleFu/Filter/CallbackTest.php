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
class DotsUnited_BundleFu_CallbackTest extends PHPUnit_Framework_TestCase
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

    public function callback()
    {
        $this->called = true;
        return 'bar';
    }
}
