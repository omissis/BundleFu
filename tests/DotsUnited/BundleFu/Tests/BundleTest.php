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
class DotsUnited_BundleFu_Tests_BundleTest extends DotsUnited_BundleFu_Tests_TestCase
{
    public function testSetOptions()
    {
        $options = array(
            'name'            => 'testbundle',
            'doc_root'        => '/my/custom/docroot',
            'bypass'          => true,
            'force'           => true,
            'render_as_xhtml' => true,
            'css_filter'      => $this->getMock('DotsUnited_BundleFu_Filter_FilterInterface'),
            'js_filter'       => $this->getMock('DotsUnited_BundleFu_Filter_FilterInterface'),
            'css_cache_path'  => 'css/cache/path',
            'js_cache_path'   => 'js/cache/path',
            'css_cache_url'   => 'css/cache/url',
            'js_cache_url'    => 'js/cache/url',
        );

        $bundle = new DotsUnited_BundleFu_Bundle();
        $bundle->setOptions($options);

        foreach ($options as $key => $val) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            $this->assertEquals($val, $bundle->$method(), ' -> ' . $key);
        }
    }

    public function testGetCssBundleUrlWithAbsoluteCssCachePathAndNoCssCacheUrlSetShouldThrowException()
    {
        $this->setExpectedException('RuntimeException', 'If you do not provide a css cache url, css cache path must be a relative local path...');
        $this->bundle->setCssCachePath('/absolute/path');
        $this->bundle->getCssBundleUrl();
    }

    public function testGetJsBundleUrlWithAbsoluteJsCachePathAndNoJsCacheUrlSetShouldThrowException()
    {
        $this->setExpectedException('RuntimeException', 'If you do not provide a js cache url, js cache path must be a relative local path...');
        $this->bundle->setJsCachePath('/absolute/path');
        $this->bundle->getJsBundleUrl();
    }

    public function testEndWithoutPriorBundleCallShouldThrowException()
    {
        $this->setExpectedException('RuntimeException', 'end() is called without a start() call.');
        $this->bundle->end();
    }

    public function testCastingInstanceToStringShouldCallRender()
    {
        $this->bundle->start();
        echo $this->includeAll();
        $this->bundle->end();

        $this->assertEquals($this->bundle->render(), (string) $this->bundle);
    }

    public function testResetResetsFileLists()
    {
        $this->bundle->start();
        echo $this->includeAll();
        $this->bundle->end();

        $this->assertGreaterThan(0, count($this->bundle->getCssFileList()));
        $this->assertGreaterThan(0, count($this->bundle->getJsFileList()));

        $this->bundle->reset();

        $this->assertEquals(0, count($this->bundle->getCssFileList()));
        $this->assertEquals(0, count($this->bundle->getJsFileList()));
    }

    public function testCustomNameShouldBeUsed()
    {
        $this->bundle->setName('custom_bundle');

        $this->assertEquals('custom_bundle.css', basename($this->bundle->getCssBundlePath()));
        $this->assertEquals('custom_bundle.js', basename($this->bundle->getJsBundlePath()));
    }

    /**
     * @todo Check why setExpectedException() is not working
     */
    public function testCastingToStringShouldNotThrowException()
    {
        //$this->setExpectedException('PHPUnit_Framework_Error_Warning');

        $callback = function($content) {
            throw new Exception('Test');
        };
        $this->bundle->setCssFilter(new DotsUnited_BundleFu_Filter_Callback($callback));
        $this->bundle->setJsFilter(new DotsUnited_BundleFu_Filter_Callback($callback));

        $this->bundle->start();
        echo $this->includeAll();
        $this->bundle->end();

        $this->assertEquals('', @$this->bundle->__toString());
    }

    /**************************************************************************/

    public function testAddCssFileShouldAcceptAbsolutePath()
    {
        $docRoot = $this->bundle->getDocRoot();
        $this->bundle->setDocRoot(null);

        $this->bundle->addCssFile($docRoot . '/css/css_1.css');

        $this->assertEquals($docRoot . '/css/css_1.css', $this->bundle->getCssFileList()->current()->getPathname());
    }

    public function testAddJsFileShouldAcceptAbsolutePath()
    {
        $docRoot = $this->bundle->getDocRoot();
        $this->bundle->setDocRoot(null);

        $this->bundle->addJsFile($docRoot . '/js/js_1.js');

        $this->assertEquals($docRoot . '/js/js_1.js', $this->bundle->getJsFileList()->current()->getPathname());
    }

    public function testExtractFilesShouldAcceptAbsolutePaths()
    {
        $docRoot = $this->bundle->getDocRoot();
        $this->bundle->setDocRoot(null);

        $str = '<link href="' . $docRoot . '/css/css_1.css"><script src="' . $docRoot . '/js/js_1.js"/>';

        $this->bundle->extractFiles($str);

        $this->assertEquals($docRoot . '/css/css_1.css', $this->bundle->getCssFileList()->current()->getPathname());
        $this->assertEquals($docRoot . '/js/js_1.js', $this->bundle->getJsFileList()->current()->getPathname());
    }

    public function testBundleShouldUseCssFilters()
    {
        $filter = $this->getMock('DotsUnited_BundleFu_Filter_FilterInterface');

        $filter
            ->expects($this->at(0))
            ->method('filterFile')
            ->will($this->returnValue('filtered1'));

        $filter
            ->expects($this->at(1))
            ->method('filter')
            ->will($this->returnValue('filtered2'));

        $this->bundle->setCssFilter($filter);

        $this->bundle->start();
        echo '<link href="/css/css_1.css?1000" media="screen" rel="stylesheet" type="text/css">';
        $this->bundle->end();

        $this->bundle->render();

        $this->assertFileMatch($this->bundle->getCssBundlePath(), 'filtered2');
    }

    public function testBundleShouldUseJsFilters()
    {
        $filter = $this->getMock('DotsUnited_BundleFu_Filter_FilterInterface');

        $filter
            ->expects($this->at(0))
            ->method('filterFile')
            ->will($this->returnValue('filtered1'));

        $filter
            ->expects($this->at(1))
            ->method('filter')
            ->will($this->returnValue('filtered2'));

        $this->bundle->setJsFilter($filter);

        $this->bundle->start();
        echo '<script src="/js/js_1.js?1000" type="text/javascript"></script>';
        $this->bundle->end();

        $this->bundle->render();

        $this->assertFileMatch($this->bundle->getJsBundlePath(), 'filtered2');
    }

    public function testSetCssCacheUrlShouldBeUsedInOutput()
    {
        $this->bundle->setCssCacheUrl('http://mycdn.org');

        $this->bundle->start();
        echo '<link href="/css/css_1.css?1000" media="screen" rel="stylesheet" type="text/css">';
        $this->bundle->end();

        $rendered = $this->bundle->render();

        $this->assertRegExp('/<link href="http:\/\/mycdn.org[^"]+" rel="stylesheet" type="text\/css">/', $rendered);
    }

    public function testSetJsCacheUrlShouldBeUsedInOutput()
    {
        $this->bundle->setJsCacheUrl('http://mycdn.org');

        $this->bundle->start();
        echo '<script src="/js/js_1.js?1000" type="text/javascript"></script>';
        $this->bundle->end();

        $rendered = $this->bundle->render();

        $this->assertRegExp('/<script src="http:\/\/mycdn.org[^"]+" type="text\/javascript"><\/script>/', $rendered);
    }

    public function testBundleShouldGenerateNonXhtmlByDefault()
    {
        $this->bundle->start();
        echo '<link href="/css/css_1.css?1000" media="screen" rel="stylesheet" type="text/css">';
        $this->bundle->end();

        $rendered = $this->bundle->render();

        $this->assertRegExp('/<link href="[^"]+" rel="stylesheet" type="text\/css">/', $rendered);
    }

    public function testBundleShouldGenerateXhtmlIfSetRenderAsXhtmlIsCalledWithTrue()
    {
        $this->bundle->setRenderAsXhtml(true);

        $this->bundle->start();
        echo '<link href="/css/css_1.css?1000" media="screen" rel="stylesheet" type="text/css">';
        $this->bundle->end();

        $rendered = $this->bundle->render();

        $this->assertRegExp('/<link href="[^"]+" rel="stylesheet" type="text\/css" \/>/', $rendered);
    }

    public function testBundleJsFilesShouldIncludeJsContent()
    {
        $this->bundle->start();
        echo $this->includeAll();
        $this->bundle->end();

        $this->bundle->render();

        $this->assertFileMatch($this->bundle->getJsBundlePath(), "function js_1()");
    }

    public function testBundleJsFilesWithAssetServerUrl()
    {
        $this->bundle->start();
        echo '<script src="https://assets.server.com/js/js_1.js?1000" type="text/javascript"></script>';
        $this->bundle->end();

        $this->bundle->render();

        $this->assertFileMatch($this->bundle->getJsBundlePath(), "function js_1()");
    }

    public function testContentRemainsSameShouldntRefreshCache()
    {
        $this->bundle->start();
        echo $this->includeSome();
        $this->bundle->end();

        $this->bundle->render();

        // check to see each bundle file exists and append some text to the bottom of each file
        $this->appendToFile($this->bundle->getCssBundlePath(), "BOGUS");
        $this->appendToFile($this->bundle->getJsBundlePath(), "BOGUS");

        $this->assertFileMatch($this->bundle->getCssBundlePath(), "BOGUS");
        $this->assertFileMatch($this->bundle->getJsBundlePath(), "BOGUS");

        $this->bundle->getCssFileList()->reset();
        $this->bundle->getJsFileList()->reset();

        $this->bundle->start();
        echo $this->includeSome();
        $this->bundle->end();

        $this->bundle->render();

        $this->assertFileMatch($this->bundle->getCssBundlePath(), "BOGUS");
        $this->assertFileMatch($this->bundle->getJsBundlePath(), "BOGUS");
    }

    public function testContentChangesShouldRefreshCache()
    {
        $this->bundle->start();
        echo $this->includeSome();
        $this->bundle->end();

        $this->bundle->render();

        $this->appendToFile($this->bundle->getCssBundlePath(), "BOGUS");
        $this->appendToFile($this->bundle->getJsBundlePath(), "BOGUS");

        $this->assertFileMatch($this->bundle->getCssBundlePath(), "BOGUS");
        $this->assertFileMatch($this->bundle->getJsBundlePath(), "BOGUS");

        $this->bundle->getCssFileList()->reset();
        $this->bundle->getJsFileList()->reset();

        $this->bundle->start();
        echo $this->includeAll();
        $this->bundle->end();

        $this->bundle->render();

        $this->assertFileNotMatch($this->bundle->getCssBundlePath(), "BOGUS");
        $this->assertFileNotMatch($this->bundle->getJsBundlePath(), "BOGUS");
    }

    public function testBundleJsOnlyShouldOutputJsIncludeStatement()
    {
        $this->bundle->start();
        list($first) = explode("\n", $this->includeSome());
        echo $first;
        $this->bundle->end();

        $output = $this->bundle->render();
        $split = explode("\n", $output);

        $this->assertEquals(1, count($split));
        $this->assertRegExp('/js/', $split[0]);
    }

    public function testBundleCssOnlyShouldOutputCssIncludeStatement()
    {
        $this->bundle->start();
        list($first, $second, $third) = explode("\n", $this->includeSome());
        echo $third;
        $this->bundle->end();

        $output = $this->bundle->render();
        $split = explode("\n", $output);

        $this->assertEquals(1, count($split));
        $this->assertRegExp('/css/', $split[0]);
    }

    public function testNonexistingFileShouldOutputFileReadErrorStatement()
    {
        $this->bundle->start();
        echo '<link href="/css/non_existing_file.css?1000" media="screen" rel="stylesheet" type="text/css">';
        echo '<script src="/js/non_existing_file.js?1000" type="text/javascript"></script>';
        $this->bundle->end();

        $this->bundle->render();

        $this->assertFileMatch($this->bundle->getCssBundlePath(), "FILE READ ERROR");
        $this->assertFileMatch($this->bundle->getJsBundlePath(), "FILE READ ERROR");
    }

    public function testBypassShouldRenderNormalOutput()
    {
        ob_start();

        $this->bundle->start(array('bypass' => true));
        echo $this->includeAll();
        $this->bundle->end();

        $contents = ob_get_clean();

        $this->bundle->render();

        $this->assertEquals($this->includeAll(), $contents);

        $this->bundle->getCssFileList()->reset();
        $this->bundle->getJsFileList()->reset();

        $this->bundle->setBypass(true);

        ob_start();

        $this->bundle->start();
        echo $this->includeSome();
        $this->bundle->end();

        $this->bundle->start();
        echo $this->includeAll();
        $this->bundle->end();

        $contents = ob_get_clean();

        $this->bundle->render();

        $this->assertEquals($this->includeSome() . $this->includeAll(), $contents);
    }

    public function testForceShouldAlwaysBundle()
    {
        $this->bundle->setForce(true);

        $this->bundle->start();
        echo $this->includeSome();
        $this->bundle->end();

        $first = $this->bundle->render();

        $this->bundle->reset();

        // Ensure we're sleeping 1 second so that the cache time changes
        sleep(1);

        $this->bundle->start();
        echo $this->includeSome();
        $this->bundle->end();

        $second = $this->bundle->render();

        $this->assertNotEquals($first, $second);
    }

    public function testBundleCssFileShouldRewriteRelativePath()
    {
        $this->bundle->start();
        echo $this->includeAll();
        $this->bundle->end();

        $this->bundle->render();

        $this->assertFileMatch($this->bundle->getCssBundlePath(), "background-image: url(/images/background.gif)");
        $this->assertFileMatch($this->bundle->getCssBundlePath(), "background-image: url(/images/groovy/background_2.gif)");
    }
}
