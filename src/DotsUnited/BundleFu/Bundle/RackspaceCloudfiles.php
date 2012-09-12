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
use DotsUnited\BundleFu\Filter\FilterInterface;
use DotsUnited\BundleFu\Filter\CssUrlRewriteFilter;

/**
 * DotsUnited\BundleFu\Bundle
 *
 * @author  Jan Sorgalla <jan.sorgalla@dotsunited.de>
 * @author  Claudio Beatrice <claudi0.beatric3@gmail.com>
 * @version @package_version@
 */
class RackspaceCloudfiles extends Bundle
{
    public function __construct(array $options = array())
    {
        parent::__construct($options);

        $this->setFileLockFlags(0);
        $this->setFsDirectorySupport(false);
    }

    protected function writeBundleFile($bundlePath, $data)
    {
        if ($this->hasFsDirectorySupport()) {
            throw new \UnexpectedValueException("This filesystem doesn't support directories.");
        }
        parent::writeBundleFile($bundlePath, $data);
    }
}