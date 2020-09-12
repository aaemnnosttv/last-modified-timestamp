<?php
/**
 * PHPUnit bootstrap file
 */

// Composer autoloader must be loaded before WP_PHPUNIT__DIR will be available
require_once dirname(__DIR__) . '/vendor/autoload.php';
putenv(sprintf('WP_PHPUNIT__TESTS_CONFIG=%s/wp-config.php', __DIR__));

if ('nightly' === getenv('WP_VERSION')) {
    $_test_root = '/tmp/wordpress-tests-lib';
} else {
    $_test_root = getenv('WP_PHPUNIT__DIR');
}

define('WP_PLUGIN_DIR', dirname(dirname(__DIR__)));

$GLOBALS['wp_tests_options'] = [
    'active_plugins' => [basename(dirname(__DIR__)) . '/last-modified-timestamp.php'],
];

// Start up the WP testing environment.
require $_test_root . '/includes/bootstrap.php';
