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
    $message = __('<strong>Last Modified Timestamp requires PHP 5.3 or higher.</strong>');

    include LAST_MODIFIED_TS__DIR . '/views/error-notice.php';
}
add_action('admin_notices', 'lastmodified_timestamp__insufficient_php');
