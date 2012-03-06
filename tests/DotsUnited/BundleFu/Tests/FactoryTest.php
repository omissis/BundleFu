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
class DotsUnited_BundleFu_Tests_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testFactoryPassesOptionsToBundle()
    {
        $options = array(
            'name'            => 'testbundle',
            'doc_root'        => '/my/custom/docroot',
            'bypass'          => true,
            'render_as_xhtml' => true,
            'css_filter'      => $this->getMock('DotsUnited_BundleFu_Filter_FilterInterface'),
            'js_filter'       => $this->getMock('DotsUnited_BundleFu_Filter_FilterInterface'),
            'css_cache_path'  => 'css/cache/path',
            'js_cache_path'   => 'js/cache/path',
            'css_cache_url'   => 'css/cache/url',
            'js_cache_url'    => 'js/cache/url',
        );

        $factory = new DotsUnited_BundleFu_Factory($options);
        $bundle = $factory->createBundle();

        foreach ($options as $key => $val) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            $this->assertEquals($val, $bundle->$method(), ' -> ' . $key);
        }
    }

    public function testFactoryResolvesFilterNames()
    {
        $cssFilter = $this->getMock('DotsUnited_BundleFu_Filter_FilterInterface');
        $jsFilter = $this->getMock('DotsUnited_BundleFu_Filter_FilterInterface');

        $factory = new DotsUnited_BundleFu_Factory(array(), array('css_filter' => $cssFilter, 'js_filter' => $jsFilter));

        $bundle = $factory->createBundle(array('css_filter' => 'css_filter', 'js_filter' => 'js_filter'));

        $this->assertEquals($cssFilter, $bundle->getCssFilter());
        $this->assertEquals($jsFilter, $bundle->getJsFilter());
    }

    public function testFactoryThrowExceptionForUnknowFilterName()
    {
        $this->setExpectedException('RuntimeException', 'There is no filter for the name "css_filter" registered.');

        $factory = new DotsUnited_BundleFu_Factory();
        $factory->createBundle(array('css_filter' => 'css_filter'));
    }

    public function testFactoryAllowsSettingNullFilters()
    {
        $factory = new DotsUnited_BundleFu_Factory(array(), array('css_filter' => null));
        $factory->createBundle(array('css_filter' => 'css_filter'));
    }

    public function testCreateBundleAcceptsArrayArgument()
    {
        $factory = new DotsUnited_BundleFu_Factory(array('name' => 'foo'));
        $bundle = $factory->createBundle(array('name' => 'bar'));

        $this->assertEquals('bar', $bundle->getName());
    }

    public function testCreateBundleAcceptsStringArgument()
    {
        $factory = new DotsUnited_BundleFu_Factory(array('name' => 'foo'));
        $bundle = $factory->createBundle('bar');

        $this->assertEquals('bar', $bundle->getName());
    }
}
