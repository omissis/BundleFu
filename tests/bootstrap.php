<?php

/*
 * This file is part of BundleFu.
 *
 * (c) 2011 Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Get base and application path
$rootPath = dirname(dirname(__FILE__));

set_include_path(implode(PATH_SEPARATOR, array(
    $rootPath . '/tests',
    $rootPath . '/src',
    get_include_path()
)));

// Setup autoloading
spl_autoload_register(function($className) {
    if (strpos($className, 'PHPUnit_') === false && strpos($className, 'DotsUnited_') === false) {
        return;
    }

    require str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
}, true, true);

unset($rootPath);
