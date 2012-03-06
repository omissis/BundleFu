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
class DotsUnited_BundleFu_Tests_FilterChainTest extends PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $filter = new DotsUnited_BundleFu_Filter_FilterChain();
        $value = 'something';
        $this->assertEquals($value, $filter->filter($value));
        $this->assertEquals($value, $filter->filterFile($value, '/js/js_1.js', new \SplFileInfo(__DIR__ . '/_files/js/js_1.js')));
    }

    public function testFilterOrder()
    {
        $filter = new DotsUnited_BundleFu_Filter_FilterChain();
        $filter->addFilter(new LowerCase())
               ->addFilter(new StripUpperCase());
        $value = 'AbC';
        $valueExpected = 'abc';
        $this->assertEquals($valueExpected, $filter->filter($value));
        $this->assertEquals($valueExpected, $filter->filterFile($value, '/js/js_1.js', new \SplFileInfo(__DIR__ . '/_files/js/js_1.js')));
    }

    public function testFilterPrependOrder()
    {
        $filter = new DotsUnited_BundleFu_Filter_FilterChain();
        $filter->appendFilter(new StripUpperCase())
               ->prependFilter(new LowerCase());
        $value = 'AbC';
        $valueExpected = 'abc';
        $this->assertEquals($valueExpected, $filter->filter($value));
        $this->assertEquals($valueExpected, $filter->filterFile($value, '/js/js_1.js', new \SplFileInfo(__DIR__ . '/_files/js/js_1.js')));
    }

    public function testFilterReset()
    {
        $filter = new DotsUnited_BundleFu_Filter_FilterChain();
        $filter->appendFilter(new StripUpperCase())
               ->prependFilter(new LowerCase());

        $filter->resetFilters();

        $value = 'AbC';
        $valueExpected = 'AbC';
        $this->assertEquals($valueExpected, $filter->filter($value));
        $this->assertEquals($valueExpected, $filter->filterFile($value, '/js/js_1.js', new \SplFileInfo(__DIR__ . '/_files/js/js_1.js')));
    }

    public function testGetFilters()
    {
        $filter = new DotsUnited_BundleFu_Filter_FilterChain();

        $filter1 = new StripUpperCase();
        $filter2 = new LowerCase();

        $filter->appendFilter($filter1)
               ->prependFilter($filter2);

        $array = $filter->getFilters();

        $this->assertEquals($filter2, $array[0]);
        $this->assertEquals($filter1, $array[1]);
    }
}


class LowerCase implements DotsUnited_BundleFu_Filter_FilterInterface
{
    public function filter($content)
    {
        return strtolower($content);
    }

    public function filterFile($content, $file, \SplFileInfo $fileInfo)
    {
        return strtolower($content);
    }
}


class StripUpperCase implements DotsUnited_BundleFu_Filter_FilterInterface
{
    public function filter($content)
    {
        return preg_replace('/[A-Z]/', '', $content);
    }

    public function filterFile($content, $file, \SplFileInfo $fileInfo)
    {
        return preg_replace('/[A-Z]/', '', $content);
    }
}
