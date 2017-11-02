<?php
/**
 * Plugin Name: Last Modified Timestamp
 * Version: 2.0.0-alpha
 * Description: Adds information to the admin interface about when each post/page was last modified (including custom post types!). Use the [last-modified] shortcode in your content!
 * Text Domain: last-modified-timestamp
 * Domain Path: /languages
 * Author: Evan Mattson
 * Author URI: https://aaemnnost.tv/
 * Plugin URI: https://github.com/aaemnnosttv/last-modified-timestamp
 */

define('LAST_MODIFIED_TS__DIR', dirname(__FILE__));

if (version_compare(phpversion(), '5.3', '<')) {
    return require_once(LAST_MODIFIED_TS__DIR . '/src/insufficient-php.php');
}

LastModifiedTimestamp::bootstrap(new LastModifiedTimestamp);
