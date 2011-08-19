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
     * Returns the result of filtering $content.
     *
     * @param mixed $content
     * @return mixed
     */
    function filter($content);
}
