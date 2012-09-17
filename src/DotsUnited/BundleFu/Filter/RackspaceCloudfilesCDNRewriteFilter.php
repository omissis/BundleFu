<?php

/*
 * This file is part of BundleFu.
 *
 * (c) 2012 Jan Sorgalla <jan.sorgalla@dotsunited.de>
 * (c) 2012 Claudio Beatrice <claudi0.beatric3@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DotsUnited\BundleFu\Filter;

/**
 * DotsUnited\BundleFu\Filter\CssUrlRewriteFilter
 *
 * @author  Jan Sorgalla <jan.sorgalla@dotsunited.de>
 * @author  Claudio Beatrice <claudi0.beatric3@gmail.com>
 * @version @package_version@
 */
class RackspaceCloudfilesCDNRewriteFilter implements FilterInterface
{
    protected $file;
    protected $bundleUrl;
    protected $namespace;

    public function __construct(array $options = array())
    {
        if (isset($options['namespace'])) {
            $this->namespace = $options['namespace'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function filter($content)
    {
        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function filterFile($content, $file, \SplFileInfo $fileInfo, $bundleUrl, $bundlePath)
    {
        $this->file = $file;
        $this->bundleUrl = $bundleUrl;

        $content = preg_replace_callback('/url\((["\']?)(?<url>.*?)(\\1)\)/', array($this, 'rewriteUrl'), $content);
        $content = preg_replace_callback('/@import (?!url\()(\'|"|)(?<url>[^\'"\)\n\r]*)\1;?/', array($this, 'rewriteUrl'), $content);

        return $content;
    }

    /**
     * Callback which rewrites matched CSS ruls.
     *
     * @param array $matches
     * @return string
     */
    protected function rewriteUrl($matches)
    {
        $matchedUrl = trim($matches['url']);

        if ('/' === $matchedUrl[0] && '/' !== $matchedUrl[1]) {
            $matches[0] = str_replace($matchedUrl, $this->namespace . $matchedUrl, $matches[0]);
            return str_replace('/', '_', $matches[0]);
        }

        // First check also matches protocol-relative urls like //example.com/images/bg.gif
        if ('/' === $matchedUrl[0] || false !== strpos($matchedUrl, '://') || 0 === strpos($matchedUrl, 'data:')) {
            return $matches[0];
        }

        $sourceUrl = dirname($this->file);

        if ('.' === $sourceUrl) {
            $sourceUrl = '/';
        }

        $path = $this->bundleUrl;

        if (false !== strpos($path, '://') || 0 === strpos($path, '//')) {
            // parse_url() does not work with protocol-relative urls
            list(, $url)  = explode('//', $path, 2);
            list(, $path) = explode('/', $url, 2);
        }

        $bundleUrl = dirname($path);

        if ('.' === $bundleUrl) {
            $bundleUrl = '/';
        }

        $url = $this->rewriteRelative($matchedUrl, $sourceUrl, $bundleUrl);

        return str_replace($matchedUrl, $url, $matches[0]);
    }

    /**
     * Rewrites to a relative url.
     *
     * @param string $url
     * @param string $sourceUrl
     * @param string $bundleUrl
     * @return string
     */
    protected function rewriteRelative($url, $sourceUrl, $bundleUrl)
    {
        if ('.' !== $url[0]) {
            $url = '/' . $sourceUrl . '/' . $url;
        }

        $url = str_replace('../', '_', $url);
        $url = str_replace('./', '_', $url);
        $url = str_replace('/', '_', $url);

        while (strstr($url, '__')) {
            $url = str_replace('__', '_', $url);
        }

        return $this->namespace . $url;
    }

    /**
     * Canonicalizes a path.
     *
     * @param string $path
     * @return string
     */
    protected function canonicalize($path)
    {
        $parts = array_filter(explode('/', $path));
        $canonicalized = array();

        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }

            if ('..' == $part) {
                array_pop($canonicalized);
            } else {
                $canonicalized[] = $part;
            }
        }

        return implode('/', $canonicalized);
    }
}
