<?php
/**
 * If we got this far, then our system requirements have been met so we are good to go.
 */

require_once(LAST_MODIFIED_TS__DIR . '/src/LastModifiedTimestamp.php');
require_once(LAST_MODIFIED_TS__DIR . '/src/TimestampFactory.php');
require_once(LAST_MODIFIED_TS__DIR . '/src/Timestamp.php');
require_once(LAST_MODIFIED_TS__DIR . '/src/functions.php');

LastModifiedTimestamp::bootstrap(new LastModifiedTimestamp);
