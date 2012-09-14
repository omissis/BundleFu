<?php

/*
 * This file is part of BundleFu.
 *
 * (c) 2012 Claudio Beatrice <claudi0.beatric3@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DotsUnited\BundleFu\Bundle;

use DotsUnited\BundleFu\Bundle;
use DotsUnited\BundleFu\Filter\RackspaceCloudfilesCDNRewriteFilter;

/**
 * DotsUnited\BundleFu\Bundle
 *
 * @author  Jan Sorgalla <jan.sorgalla@dotsunited.de>
 * @author  Claudio Beatrice <claudi0.beatric3@gmail.com>
 * @version @package_version@
 */
class RackspaceCloudfiles extends Bundle
{
    /**
     * Whether or not perform the write of the assets on Rackspace Cloudfiles.
     * It's disabled by default because it's very slow and can't be used in production.
     *
     * @var boolean
     */
    private $performWrite = false;

    /**
     * Checksum to prepend to filenames in order to bust the CDN cache on demand.
     *
     * @var string
     */
    private $checksum = null;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        // On rackspace we won't use a URL Parameter to bust the cache since it won't work:
        // the CDN caches the files and has a TTL of at least 1 hour. This way we will avoid
        // that limit and we'll be able to control the busting at deploy time,
        // even though it comes at the cost of having zombie files on the CDN.
        $this->cssTemplate = '<link href="%s%s" rel="stylesheet" type="text/css"%s>';
        $this->jsTemplate  = '<script src="%s%s" type="text/javascript"></script>';

        if (!array_key_exists('css_filter', $options)) {
            $options['css_filter'] = new RackspaceCloudfilesCDNRewriteFilter();
        }

        $this->setOptions($options);

        $this->setFileLockFlags(0);
        $this->setFsDirectorySupport(false);
    }

    public function setPerformWrite($performWrite)
    {
        $this->performWrite = $performWrite;
        return $this;
    }

    public function getPerformWrite()
    {
        return $this->performWrite;
    }

    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;
        return $this;
    }

    public function getChecksum()
    {
        return empty($this->checksum) ? null : $this->checksum . '_';
    }

    /**
     * Render out the css bundle.
     *
     * The write on the filesystem, since very expensive,
     * is controlled by a user-defined flag which can be controlled at deploy time
     *
     * @return string
     */
    public function renderCss()
    {
        $cssFileList = $this->getCssFileList();

        if ($cssFileList->count() == 0) {
            return '';
        }

        $bundlePath = $this->getCssBundlePath();
        $bundleUrl  = $this->getCssBundleUrl();

        if ($this->getPerformWrite()) {
            $data   = '';
            $filter = $this->getCssFilter();

            foreach ($cssFileList as $file => $fileInfo) {
                $data .= '/* --------- ' . $file . ' --------- */' . PHP_EOL;
                $contents = @file_get_contents($fileInfo->getPathname());
                if (!$contents) {
                    $data .= '/* FILE READ ERROR! */' . PHP_EOL;
                } else {
                    if (null !== $filter) {
                        $contents = $filter->filterFile($contents, $file, $fileInfo, $bundleUrl, $bundlePath);
                    }

                    $data .= $contents . PHP_EOL;
                }
            }

            if (null !== $filter) {
                $data = $filter->filter($data);
            }

            $this->writeBundleFile($bundlePath, $data);
        }

        $template = $this->getCssTemplate();

        if (is_callable($template)) {
            return call_user_func($template, $this->getChecksum(), $bundleUrl, $this->getRenderAsXhtml());
        }

        return sprintf(
            $template,
            $this->getChecksum(),
            $bundleUrl,
            $this->getRenderAsXhtml() ? ' /' : ''
        );
    }

    /**
     * Render out the javascript bundle.
     *
     * @return string
     */
    public function renderJs()
    {
        $jsFileList = $this->getJsFileList();

        if ($jsFileList->count() == 0) {
            return '';
        }

        $bundlePath = $this->getJsBundlePath();
        $bundleUrl = $this->getJsBundleUrl();

        if ($this->getPerformWrite()) {
            $data   = '';
            $filter = $this->getJsFilter();

            foreach ($jsFileList as $file => $fileInfo) {
                $data .= '/* --------- ' . $file . ' --------- */' . PHP_EOL;
                $contents = @file_get_contents($fileInfo->getPathname());
                if (!$contents) {
                    $data .= '/* FILE READ ERROR! */' . PHP_EOL;
                } else {
                    if (null !== $filter) {
                        $contents = $filter->filterFile($contents, $file, $fileInfo, $bundleUrl, $bundlePath);
                    }

                    $data .= $contents . PHP_EOL;
                }
            }

            if (null !== $filter) {
                $data = $filter->filter($data);
            }

            $this->writeBundleFile($bundlePath, $data);
        }

        $template = $this->getJsTemplate();

        if (is_callable($template)) {
            return call_user_func($template, $this->getChecksum(), $bundleUrl);
        }

        return sprintf(
            $template,
            $this->getChecksum(),
            $bundleUrl
        );
    }

    protected function writeBundleFile($bundlePath, $data)
    {
        if ($this->hasFsDirectorySupport()) {
            throw new \UnexpectedValueException("This filesystem doesn't support directories.");
        }
        return parent::writeBundleFile($bundlePath, $data);
    }
}