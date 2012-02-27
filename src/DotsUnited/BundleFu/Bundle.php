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
 * DotsUnited_BundleFu_Bundle
 *
 * @author  Jan Sorgalla <jan.sorgalla@dotsunited.de>
 * @version @package_version@
 */
class DotsUnited_BundleFu_Bundle
{
    /**
     * Whether to bypass capturing.
     *
     * @var boolean
     */
    protected $bypass = false;

    /**
     * Directory in which to look for files.
     *
     * @var string
     */
    protected $docRoot;

    /**
     * Bundle name.
     *
     * @var string
     */
    protected $name;

    /**
     * Directory in which to write bundled css files.
     *
     * @var string
     */
    protected $cssCachePath = 'css/cache';

    /**
     * Directory in which to write bundled javascript files.
     *
     * @var string
     */
    protected $jsCachePath = 'js/cache';

    /**
     * Path the generated css bundles are publicly accessible under.
     *
     * Optional. If not set, $this->cssCachePath is used.
     *
     * @var string
     */
    protected $cssCacheUrl;

    /**
     * Path the generated javascript bundles are publicly accessible under.
     *
     * Optional. If not set, $this->jsCachePath is used.
     *
     * @var string
     */
    protected $jsCacheUrl;

    /**
     * Whether to render as XHTML.
     *
     * @var boolean
     */
    protected $renderAsXhtml = false;

    /**
     * CSS file list.
     *
     * @var FileList
     */
    protected $cssFileList;

    /**
     * Javascript file list.
     *
     * @var FileList
     */
    protected $jsFileList;

    /**
     * CSS filter.
     *
     * @var Filter
     */
    protected $cssFilter;

    /**
     * CSS filter.
     *
     * @var Filter
     */
    protected $jsFilter;

    /**
     * CSS url rewriter.
     *
     * @var CssUrlRewriter
     */
    protected $cssUrlRewriter;

    /**
     * Options for bundling in process.
     *
     * @var array
     */
    protected $currentBundleOptions;

    /**
     * Allows to pass options as array.
     *
     * @param array $options
     * @return Bundle
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $val) {
            switch ($key) {
                case 'name':
                    $this->setName($val);
                    break;
                case 'doc_root':
                    $this->setDocRoot($val);
                    break;
                case 'bypass':
                    $this->setBypass($val);
                    break;
                case 'render_as_xhtml':
                    $this->setRenderAsXhtml($val);
                    break;
                case 'css_filter':
                    $this->setCssFilter($val);
                    break;
                case 'js_filter':
                    $this->setJsFilter($val);
                    break;
                case 'css_cache_path':
                    $this->setCssCachePath($val);
                    break;
                case 'js_cache_path':
                    $this->setJsCachePath($val);
                    break;
                case 'css_cache_url':
                    $this->setCssCacheUrl($val);
                    break;
                case 'js_cache_url':
                    $this->setJsCacheUrl($val);
                    break;
            }
        }

        return $this;
    }

    /**
     * Set whether to bypass capturing.
     *
     * @param boolean $bypass
     * @return Bundle
     */
    public function setBypass($bypass)
    {
        $this->bypass = $bypass;
        return $this;
    }

    /**
     * Get whether to bypass capturing.
     *
     * @return boolean
     */
    public function getBypass()
    {
        return $this->bypass ;
    }

    /**
     * Set directory in which to look for files.
     *
     * @param string $docRoot
     * @return Bundle
     */
    public function setDocRoot($docRoot)
    {
        $this->docRoot = $docRoot;
        return $this;
    }

    /**
     * Get directory in which to look for files.
     *
     * @return string
     */
    public function getDocRoot()
    {
        return $this->docRoot;
    }

    /**
     * Set the bundle name.
     *
     * @param string $name
     * @return Bundle
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the bundle name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set directory in which to write bundled css files.
     *
     * @param string $cssCachePath
     * @return Bundle
     */
    public function setCssCachePath($cssCachePath)
    {
        $this->cssCachePath = $cssCachePath;
        return $this;
    }

    /**
     * Get directory in which to write bundled css files.
     *
     * @return string
     */
    public function getCssCachePath()
    {
        return $this->cssCachePath;
    }

    /**
     * Set directory in which to write bundled javascript files.
     *
     * @param string $jsCachePath
     * @return Bundle
     */
    public function setJsCachePath($jsCachePath)
    {
        $this->jsCachePath = $jsCachePath;
        return $this;
    }

    /**
     * Get directory in which to write bundled javascript files.
     *
     * @return string
     */
    public function getJsCachePath()
    {
        return $this->jsCachePath;
    }

    /**
     * Set path the generated css bundles are publicly accessible under.
     *
     * @param string $cssCacheUrl
     * @return Bundle
     */
    public function setCssCacheUrl($cssCacheUrl)
    {
        $this->cssCacheUrl = $cssCacheUrl;
        return $this;
    }

    /**
     * Get path the generated css bundles are publicly accessible under.
     *
     * @return string
     */
    public function getCssCacheUrl()
    {
        return $this->cssCacheUrl ;
    }

    /**
     * Set path the generated javascript bundles are publicly accessible under.
     *
     * @param string $jsCacheUrl
     * @return Bundle
     */
    public function setJsCacheUrl($jsCacheUrl)
    {
        $this->jsCacheUrl = $jsCacheUrl;
        return $this;
    }

    /**
     * Get path the generated javascript bundles are publicly accessible under.
     *
     * @return string
     */
    public function getJsCacheUrl()
    {
        return $this->jsCacheUrl ;
    }

    /**
     * Set whether to render as XHTML.
     *
     * @param  boolean $renderAsXhtml
     * @return Bundle
     */
    public function setRenderAsXhtml($renderAsXhtml)
    {
        $this->renderAsXhtml = $renderAsXhtml;
        return $this;
    }

    /**
     * Get whether to render as XHTML.
     *
     * @return boolean
     */
    public function getRenderAsXhtml()
    {
        return $this->renderAsXhtml;
    }

    /**
     * Get css file list.
     *
     * @return FileList
     */
    public function getCssFileList()
    {
        if (null === $this->cssFileList) {
            $this->cssFileList = new DotsUnited_BundleFu_FileList();
        }

        return $this->cssFileList;
    }

    /**
     * Get javascript file list.
     *
     * @return FileList
     */
    public function getJsFileList()
    {
        if (null === $this->jsFileList) {
            $this->jsFileList = new DotsUnited_BundleFu_FileList();
        }

        return $this->jsFileList;
    }

    /**
     * Set css filter.
     *
     * @param DotsUnited_BundleFu_Filter_FilterInterface
     * @return Bundle
     */
    public function setCssFilter(DotsUnited_BundleFu_Filter_FilterInterface $filter = null)
    {
        $this->cssFilter = $filter;
        return $this;
    }

    /**
     * Get css filter.
     *
     * @return DotsUnited_BundleFu_Filter_FilterInterface
     */
    public function getCssFilter()
    {
        return $this->cssFilter;
    }

    /**
     * Set javascript filter.
     *
     * @param DotsUnited_BundleFu_Filter_FilterInterface
     * @return Bundle
     */
    public function setJsFilter(DotsUnited_BundleFu_Filter_FilterInterface $filter = null)
    {
        $this->jsFilter = $filter;
        return $this;
    }

    /**
     * Get javascript filter.
     *
     * @return DotsUnited_BundleFu_Filter_FilterInterface
     */
    public function getJsFilter()
    {
        return $this->jsFilter;
    }

    /**
     * Get css url rewriter.
     *
     * @return DotsUnited_BundleFu_CssUrlRewriter
     */
    public function getCssUrlRewriter()
    {
        if (null === $this->cssUrlRewriter) {
            $this->cssUrlRewriter = new DotsUnited_BundleFu_CssUrlRewriter();
        }

        return $this->cssUrlRewriter;
    }

    /**
     * Get css bundle path.
     *
     * @return string
     */
    public function getCssBundlePath()
    {
        $cacheDir = $this->getCssCachePath();

        if ($this->isRelativePath($cacheDir)) {
            $cacheDir = $this->getDocRoot() . DIRECTORY_SEPARATOR . $cacheDir;
        }

        $name = $this->getName();

        if (null === $name) {
            $name = sprintf('bundle_%s', $this->getCssFileList()->getHash());
        }

        return sprintf(
            "%s%s%s.css",
            $cacheDir,
            DIRECTORY_SEPARATOR,
            $name
        );
    }

    /**
     * Get javascript bundle path.
     *
     * @return string
     */
    public function getJsBundlePath()
    {
        $cacheDir = $this->getJsCachePath();

        if ($this->isRelativePath($cacheDir)) {
            $cacheDir = $this->getDocRoot() . DIRECTORY_SEPARATOR . $cacheDir;
        }

        $name = $this->getName();

        if (null === $name) {
            $name = sprintf('bundle_%s', $this->getJsFileList()->getHash());
        }

        return sprintf(
            "%s%s%s.js",
            $cacheDir,
            DIRECTORY_SEPARATOR,
            $name
        );
    }

    /**
     * Get css bundle url.
     *
     * @return string
     */
    public function getCssBundleUrl()
    {
        $url = $this->getCssCacheUrl();

        if (!$url) {
            $url = $this->getCssCachePath();

            if (!$this->isRelativePath($url)) {
                throw new RuntimeException('If you do not provide a css cache url, css cache path must be a relative local path...');
            }

            $url = '/' . str_replace(DIRECTORY_SEPARATOR, '/', $url);
        }

        $name = $this->getName();

        if (null === $name) {
            $name = sprintf('bundle_%s', $this->getCssFileList()->getHash());
        }

        return sprintf(
            "%s/%s.css",
            $url,
            $name
        );
    }

    /**
     * Get javascript bundle url.
     *
     * @return string
     */
    public function getJsBundleUrl()
    {
        $url = $this->getJsCacheUrl();

        if (!$url) {
            $url = $this->getJsCachePath();

            if (!$this->isRelativePath($url)) {
                throw new RuntimeException('If you do not provide a js cache url, js cache path must be a relative local path...');
            }

            $url = '/' . str_replace(DIRECTORY_SEPARATOR, '/', $url);
        }

        $name = $this->getName();

        if (null === $name) {
            $name = sprintf('bundle_%s', $this->getJsFileList()->getHash());
        }

        return sprintf(
            "%s/%s.js",
            $url,
            $name
        );
    }

    /**
     * Add a CSS file.
     *
     * @param string $file
     * @param string $docRoot
     * @return Bundle
     */
    public function addCssFile($file, $docRoot = null)
    {
        if (!$docRoot) {
            $docRoot = $this->getDocRoot();
        }

        $file    = preg_replace('/^https?:\/\/[^\/]+/i', '', $file);
        $abspath = $docRoot . DIRECTORY_SEPARATOR . $file;

        $this->getCssFileList()->addFile($file, $abspath);

        return $this;
    }

    /**
     * Add a javascript file.
     *
     * @param string $file
     * @param string $docRoot
     * @return Bundle
     */
    public function addJsFile($file, $docRoot = null)
    {
        if (!$docRoot) {
            $docRoot = $this->getDocRoot();
        }

        $file    = preg_replace('/^https?:\/\/[^\/]+/i', '', $file);
        $abspath = $docRoot . DIRECTORY_SEPARATOR . $file;

        $this->getJsFileList()->addFile($file, $abspath);

        return $this;
    }

    /**
     * Start capturing and bundling current output.
     *
     * @param array $options
     * @return Bundle
     */
    public function start(array $options = array())
    {
        $currentBundleOptions = array(
            'docroot' => $this->getDocRoot(),
            'bypass'  => $this->getBypass()
        );

        $this->currentBundleOptions = array_merge($currentBundleOptions, $options);
        ob_start();

        return $this;
    }

    /**
     * End capturing and bundling current output.
     *
     * @param array $options
     * @return Bundle
     */
    public function end(array $options = array())
    {
        if (null === $this->currentBundleOptions) {
            throw new RuntimeException('end() is called without a start() call.');
        }

        $options = array_merge($this->currentBundleOptions, $options);

        if (empty($options['docroot'])) {
            throw new RuntimeException('Please set a document root either with setDocRoot() or via runtime through bundle options.');
        }

        $captured = ob_get_clean();

        if ($options['bypass']) {
            echo $captured;
        } else {
            $this->extractFiles($captured, $options['docroot']);
        }

        $this->currentBundleOptions = null;

        return $this;
    }

    /**
     * Extract files from HTML.
     *
     * @param string $html
     * @param string $docRoot
     * @return Bundle
     */
    public function extractFiles($html, $docRoot = null)
    {
        if (!$docRoot) {
            $docRoot = $this->getDocRoot();
        }

        preg_match_all('/(href|src) *= *["\']([^"^\'^\?]+)/i', $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (strtolower($match[1]) == 'src') {
                $this->addJsFile($match[2], $docRoot);
            } else {
                $this->addCssFile($match[2], $docRoot);
            }
        }

        return $this;
    }

    /**
     * Reset the bundle.
     *
     * @return Bundle
     */
    public function reset()
    {
        $this->getCssFileList()->reset();
        $this->getJsFileList()->reset();
        return $this;
    }

    /**
     * Render out all bundles.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $return = $this->render();
            return $return;
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
            return '';
        }
    }

    /**
     * Render out all bundle.
     *
     * @return string
     */
    public function render()
    {
        return trim($this->renderCss() . PHP_EOL . $this->renderJs());
    }

    /**
     * Render out the css bundle.
     *
     * @return string
     */
    public function renderCss()
    {
        $cssFileList = $this->getCssFileList();

        if ($cssFileList->count() == 0) {
            return '';
        }

        $cacheFile = $this->getCssBundlePath();
        $cacheTime = @filemtime($cacheFile);

        if (false === $cacheTime || $cacheTime < $cssFileList->getMaxMTime()) {
            $data = '';

            $cssUrlRewriter = $this->getCssUrlRewriter();

            foreach ($cssFileList as $file => $fileInfo) {
                $data .= '/* --------- ' . $file . ' --------- */' . PHP_EOL;
                $contents = @file_get_contents($fileInfo->getPathname());
                if (!$contents) {
                    $data .= '/* FILE READ ERROR! */' . PHP_EOL;
                } else {
                    $data .= $cssUrlRewriter->rewriteUrls($file, $contents) . PHP_EOL;
                }
            }

            $filter = $this->getCssFilter();
            if (null !== $filter) {
                $data = $filter->filter($data);
            }

            $dir = dirname($cacheFile);

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            if (false === file_put_contents($cacheFile, $data, LOCK_EX)) {
                throw new RuntimeException('Cannot write css cache file to "' . $cacheFile . '"');
            }

            $cacheTime = filemtime($cacheFile);
        }

        return sprintf(
            '<link href="%s?%s" rel="stylesheet" type="text/css"%s>',
            $this->getCssBundleUrl(),
            $cacheTime,
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

        $cacheFile = $this->getJsBundlePath();
        $cacheTime = @filemtime($cacheFile);

        if (false === $cacheTime || $cacheTime < $jsFileList->getMaxMTime()) {
            $data = '';

            foreach ($jsFileList as $file => $fileInfo) {
                $data .= '/* --------- ' . $file . ' --------- */' . PHP_EOL;
                $contents = @file_get_contents($fileInfo->getPathname());
                if (!$contents) {
                    $data .= '/* FILE READ ERROR! */' . PHP_EOL;
                } else {
                    $data .= $contents . PHP_EOL;
                }
            }

            $filter = $this->getJsFilter();
            if (null !== $filter) {
                $data = $filter->filter($data);
            }

            $dir = dirname($cacheFile);

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            if (false === file_put_contents($cacheFile, $data, LOCK_EX)) {
                throw new RuntimeException('Cannot write js cache file to "' . $cacheFile . '"');
            }

            $cacheTime = filemtime($cacheFile);
        }

        return sprintf(
            '<script src="%s?%s" type="text/javascript"></script>',
            $this->getJsBundleUrl(),
            $cacheTime
        );
    }

    /**
     * Check whether $path is a local relative path.
     *
     * @param string $path
     * @return boolean
     */
    public function isRelativePath($path)
    {
        return strpos($path, '://') === false && !preg_match('/^\\//', $path) && !preg_match('/^[A-Z]:\\\\/i', $path);
    }
}
