<?php

/**
 * Override to prevent fatal.
 */
function get_the_last_modified_timestamp() {}

require_once('functions.php');

/**
 * Output an error message in wp-admin to notify about insufficient requirements.
 */
function lastmodified_timestamp__insufficient_php()
{
    $message = sprintf(
        /* translators: %s: PHP version number */
        __('Last Modified Timestamp requires PHP %s or higher.', 'last-modified-timestamp'),
        LAST_MODIFIED_TS__PHP_MINIMUM
    );

    include LAST_MODIFIED_TS__DIR . '/views/error-notice.php';
}
add_action('admin_notices', 'lastmodified_timestamp__insufficient_php');
add_action('network_admin_notices', 'lastmodified_timestamp__insufficient_php');
