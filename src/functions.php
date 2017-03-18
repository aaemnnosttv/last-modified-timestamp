<?php

function get_the_last_modified_timestamp( $context = null, $override = null )
{
	return LastModifiedTimestamp::get_instance()->construct_timestamp( $context, $override );
}

function the_last_modified_timestamp( $context = null, $override = null )
{
	echo get_the_last_modified_timestamp( $context, $override );
}