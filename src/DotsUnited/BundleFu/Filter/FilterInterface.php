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
 * DotsUnited_BundleFu_Filter_FilterInterface
 *
 * @author  Jan Sorgalla <jan.sorgalla@dotsunited.de>
 * @version @package_version@
 */
interface DotsUnited_BundleFu_Filter_FilterInterface
{
    /**
     * Filter applied to concenated content before its written to the cache file.
     *
     * @param mixed $content
     * @return mixed
     */
    function filter($content);
    
    /**
     * Filter applied to a single file after it has beed loaded.
     *
     * @param mixed $content
     * @param string $file File as it appears in the href/src attribute
     * @param \SplFileInfo $fileInfo
     * @return mixed
     */
    function filterFile($content, $file, \SplFileInfo $fileInfo);
}
