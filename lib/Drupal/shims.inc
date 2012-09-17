<?php

/**
 * This file implements functions needed by the StreamWrapperInterface,
 * respecting the signature but changing the implementation.
 */

define('CACHE_TEMPORARY', 0);
define('DRUPAL_ROOT', __DIR__);
define('WATCHDOG_NOTICE', 5);
define('WATCHDOG_DEBUG', 7);

/**
 * Fake Drupal Variable storage
 */
class Drupal_Variables
{
    static private $defaults = array(
        'rackspace_cloud_debug'      => true,
        'rackspace_cloud_cdn_domain' => null,
        'rackspace_cloud_container'  => null,
        'rackspace_cloud_username'   => null,
        'rackspace_cloud_api_key'    => null,
        'rackspace_cloud_auth_url'   => null,
    );

    static function get($name, $default) {
        return isset(self::$defaults[$name]) ? self::$defaults[$name] : $default;
    }

    static function set($name, $value) {
        self::$defaults[$name] = $value;
    }
}

/**
 * @see http://api.drupal.org/api/drupal/includes%21bootstrap.inc/function/watchdog/7
 */
function watchdog($type, $message, $variables = array(), $severity = WATCHDOG_NOTICE, $link = NULL) {
    return null;
}

/**
 * @see http://api.drupal.org/api/drupal/includes%21bootstrap.inc/function/variable_get/7
 */
function variable_get($name, $default = null) {
    return Drupal_Variables::get($name, $default);
}

/**
 * @see http://api.drupal.org/api/drupal/includes%21bootstrap.inc/function/variable_set/7
 */
function variable_set($name, $value) {
    Drupal_Variables::set($name, $value);
}

/**
 * @see http://api.drupal.org/api/drupal/includes%21cache.inc/function/cache_set/7
 */
function cache_set($cid, $data, $bin = 'cache', $expire = CACHE_TEMPORARY) {
    return true;
}

/**
 * @see http://api.drupal.org/api/drupal/includes%21cache.inc/function/cache_get/7
 */
function cache_get($cid, $bin = 'cache') {
    return false;
}

/**
 * @see http://drupal.org/project/libraries
 */
function libraries_load($name, $variant = NULL) {
    if ($name === 'rackspace') {
        require_once 'cloudfiles.php';
        return true;
    }

    return false;
}

/**
 * @see http://api.drupal.org/api/drupal/includes%21bootstrap.inc/function/drupal_static/7
 */
function &drupal_static($name, $default_value = NULL, $reset = FALSE) {
  static $data = array(), $default = array();
  // First check if dealing with a previously defined static variable.
  if (isset($data[$name]) || array_key_exists($name, $data)) {
    // Non-NULL $name and both $data[$name] and $default[$name] statics exist.
    if ($reset) {
      // Reset pre-existing static variable to its default value.
      $data[$name] = $default[$name];
    }
    return $data[$name];
  }
  // Neither $data[$name] nor $default[$name] static variables exist.
  if (isset($name)) {
    if ($reset) {
      // Reset was called before a default is set and yet a variable must be
      // returned.
      return $data;
    }
    // First call with new non-NULL $name. Initialize a new static variable.
    $default[$name] = $data[$name] = $default_value;
    return $data[$name];
  }
  // Reset all: ($name == NULL). This needs to be done one at a time so that
  // references returned by earlier invocations of drupal_static() also get
  // reset.
  foreach ($default as $name => $value) {
    $data[$name] = $value;
  }
  // As the function returns a reference, the return should always be a
  // variable.
  return $data;
}

/**
 * @see http://api.drupal.org/api/drupal/includes%21module.inc/function/drupal_alter/7
 */
function drupal_alter($type, &$data, &$context1 = NULL, &$context2 = NULL) {
    return null;
}