<?php

if ( ! function_exists('get_the_last_modified_timestamp')) :
    /**
     * @param null $context
     * @param null $override
     *
     * @return string
     */
    function get_the_last_modified_timestamp($context = null, $override = null)
    {
        return LastModifiedTimestamp::get_instance()->construct_timestamp($context, $override);
    }
endif;

if ( ! function_exists('the_last_modified_timestamp')) :
    /**
     * @param null $context
     * @param null $override
     */
    function the_last_modified_timestamp($context = null, $override = null)
    {
        echo get_the_last_modified_timestamp($context, $override);
    }
endif;
