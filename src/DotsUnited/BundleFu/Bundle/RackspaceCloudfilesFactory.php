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

namespace DotsUnited\BundleFu\Bundle;

use DotsUnited\BundleFu\Factory;

/**
 * DotsUnited\BundleFu\Factory
 *
 * @author  Claudio Beatrice <claudi0.beatric3@gmail.com>
 * @version @package_version@
 */
class RackspaceCloudfilesFactory extends Factory
{
    /**
     * Create a Bundle\RackspaceCloudfiles instance.
     *
     * @param string|array $options An array of options or a bundle name as string
     * @return \DotsUnited\BundleFu\Bundle
     * @throws \RuntimeException
     */
    public function createBundle($options = null)
    {
        if (!is_array($options)) {
            if (null !== $options) {
                $options = array('name' => $options);
            } else {
                $options = array();
            }
        }

        $options = array_merge($this->options, $options);

        if (isset($options['css_filter']) && is_string($options['css_filter'])) {
            $options['css_filter'] = $this->getFilter($options['css_filter']);
        }

        if (isset($options['js_filter']) && is_string($options['js_filter'])) {
            $options['js_filter'] = $this->getFilter($options['js_filter']);
        }

        return new RackspaceCloudfiles($options);
    }
}