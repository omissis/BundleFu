<?php

/*
 * This file is part of BundleFu.
 *
 * (c) 2011 Jan Sorgalla <jan.sorgalla@dotsunited.de>
 * (c) 2012 Claudio Beatrice <claudi0.beatric3@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DotsUnited\BundleFu\Tests\Bundle;

use DotsUnited\BundleFu\Bundle;
use DotsUnited\BundleFu\Filter\CallbackFilter;
use DotsUnited\BundleFu\Tests\TestCase;

/**
 * @author  Claudio Beatrice <claudi0.beatric3@gmail.com>
 * @version @package_version@
 */
class RackspaceCloudfilesTest extends TestCase
{
    private $apiKey;
    private $authUrl;
    private $containerName;
    private $cdnContainerUrl;
    private $username;

    public function __construct()
    {
        // sets up stream wrapper
        require_once 'Drupal/shims.inc';
        require_once 'Drupal/includes/file.inc';
        require_once 'Drupal/includes/stream_wrappers.inc';
        require_once 'Drupal/rackspacecloudfiles_streams.inc';

        if (file_exists(__DIR__ . '/parameters.ini')) {
            $parameters = parse_ini_file(__DIR__ . '/parameters.ini');

            $this->apiKey          = $parameters['apiKey'];
            $this->authUrl         = $parameters['authUrl'];
            $this->cdnContainerUrl = $parameters['cdnContainerUrl'];
            $this->containerName   = $parameters['containerName'];
            $this->username        = $parameters['username'];
        }

        variable_set('rackspace_cloud_api_key',    $this->apiKey);
        variable_set('rackspace_cloud_auth_url',   $this->authUrl);
        variable_set('rackspace_cloud_container',  $this->containerName);
        variable_set('rackspace_cloud_cdn_domain', $this->cdnContainerUrl);
        variable_set('rackspace_cloud_username',   $this->username);
    }

    protected function checkSkipTest()
    {
        if (empty($this->username) || empty($this->apiKey) || empty($this->cdnContainerUrl)) {
            $this->markTestSkipped(
                'You must provide username, apiKey and cdnContainerUrl. Check http://www.rackspace.com/cloud/public/files/resources/ for more information.'
            );
        }
    }

    public function setUp()
    {
        $this->checkSkipTest();

        $this->bundle = new Bundle\RackspaceCloudfiles();
        $this->bundle->setDocRoot(__DIR__ . '/_files');

        stream_wrapper_register('rscf', 'RackspaceCloudFilesStreamWrapper')
            or die("Failed to register rscf:// protocol");
    }

    public function tearDown()
    {
        parent::tearDown();

        stream_wrapper_unregister('rscf')
            or die("Failed to register rscf:// protocol");
    }

    protected function purgeCache()
    {
        // TODO: implement
    }

    public function testCopyFile()
    {
        $this->assertTrue(copy(__DIR__ . '/../_files/css/css_3.css', 'rscf://css_3.css'));
    }

    public function testDeleteFile()
    {
        $this->assertTrue(unlink('rscf://css_3.css'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage file_put_contents(): Exclusive locks may only be set for regular files
     */
    public function testWriteCssOnCDNContainerUsingExclusiveLock()
    {
        $this->bundle->setFileLockFlags(LOCK_EX);

        $this->bundle->setCssCachePath('rscf:///');
        $this->bundle->setCssCacheUrl($this->cdnContainerUrl);

        $this->bundle->start();
        echo '<link href="/css/css_1.css?1000" media="screen" rel="stylesheet" type="text/css">';
        $this->bundle->end();

        $this->bundle->render();
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage file_put_contents(): Exclusive locks may only be set for regular files
     */
    public function testWriteJsOnCDNContainerUsingExclusiveLock()
    {
        $this->bundle->setFileLockFlags(LOCK_EX);

        $this->bundle->setJsCachePath('rscf:///');
        $this->bundle->setJsCacheUrl($this->cdnContainerUrl);

        $this->bundle->start();
        echo '<script src="/js/js_1.js?1000" type="text/javascript"></script>';
        $this->bundle->end();

        $this->bundle->render();
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage This filesystem doesn't support directories.
     */
    public function testFailWriteCssOnCDNContainerUsingDirectories()
    {
        $this->bundle->setCssCachePath('rscf://foo/');
        $this->bundle->setCssCacheUrl($this->cdnContainerUrl);
        $this->bundle->setFsDirectorySupport(true);

        $this->bundle->start();
        echo '<link href="/css/css_1.css?1000" media="screen" rel="stylesheet" type="text/css">';
        $this->bundle->end();

        $this->bundle->render();
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage This filesystem doesn't support directories.
     */
    public function testFailWriteJsOnCDNContainerUsingDirectories()
    {
        $this->bundle->setJsCachePath('rscf://foo/');
        $this->bundle->setJsCacheUrl($this->cdnContainerUrl);
        $this->bundle->setFsDirectorySupport(true);

        $this->bundle->start();
        echo '<script src="/js/js_1.js?1000" type="text/javascript"></script>';
        $this->bundle->end();

        $this->bundle->render();
    }

    public function testWriteCssOnCDNContainer()
    {
        $this->bundle->setCssCachePath('rscf:///');
        $this->bundle->setCssCacheUrl($this->cdnContainerUrl);

        $this->bundle->start();
        echo '<link href="/css/css_1.css?1000" media="screen" rel="stylesheet" type="text/css">';
        $this->bundle->end();

        $rendered = $this->bundle->render();

        $this->assertRegExp('/<link href="' . addcslashes($this->cdnContainerUrl, '.:/') . '[^"]+" rel="stylesheet" type="text\/css">/', $rendered);
        $this->assertTrue(unlink($this->bundle->getCssBundlePath()));
    }

    public function testWriteJsOnCDNContainer()
    {
        $this->bundle->setJsCachePath('rscf:///');
        $this->bundle->setJsCacheUrl($this->cdnContainerUrl);

        $this->bundle->start();
        echo '<script src="/js/js_1.js?1000" type="text/javascript"></script>';
        $this->bundle->end();

        $rendered = $this->bundle->render();

        $this->assertRegExp('/<script src="' . addcslashes($this->cdnContainerUrl, '.:/') . '[^"]+" type="text\/javascript"><\/script>/', $rendered);
        $this->assertTrue(unlink($this->bundle->getJsBundlePath()));
    }
}