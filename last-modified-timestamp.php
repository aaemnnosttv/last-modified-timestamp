<?php
/*
Plugin Name: Last Modified Timestamp
Description: This plugin adds information to the admin interface about when each post/page was last modified (including custom post types!). Use the [last-modified] shortcode in your content!
Version: 1.0
Author: Evan Mattson
*/

/*  Copyright 2011 Evan Mattson (email: evanmattson at gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


class LastModifiedTimestamp
{

	public static $defaults;

	// PHP 4 Constructor
	function LastModifiedTimestamp() {
		$this->__construct();
	}
	function __construct()
	{
		add_action( 'plugins_loaded',	array( &$this, 'setup_defaults' ) );
		add_action( 'admin_init',		array( &$this, 'admin_actions' ), 1 );

		add_shortcode( 'last-modified',	array( &$this, 'shortcode_handler' ) );
	}

	function setup_defaults()
	{
		$d = array();
		// base defaults
		$d['base'] = array(
			'datef'  => 'M j, Y',
			'timef'  => null,
			'sep'    => '@',
			'format' => '%date% %sep% %time%'
		);
		// extended contextual defaults
		$d['contexts'] = array(
			'messages'    	=> array(),
			'publish-box' 	=> array(),
			'shortcode' 	=> array(),
			'wp-table'    	=> array(
				'datef' => 'Y/m/d',
				'sep'   => '<br />'
			),
		);

		static::$defaults = $d;
	}

	function admin_actions()
	{
		add_action( 'admin_print_styles-edit.php',			array( &$this, 'print_admin_css' ) );
		add_action( 'admin_print_styles-post.php',			array( &$this, 'print_admin_css' ) );
		add_action( 'admin_print_styles-post-new.php',		array( &$this, 'print_admin_css' ) );
		add_action( 'post_submitbox_misc_actions',			array( &$this, 'publish_box'	 ), 1 );  // NEW PRIORITY

		add_filter( 'post_updated_messages',				array( &$this, 'modify_messages' ) );

		foreach ( get_post_types() as $pt )
		{
			add_filter( "manage_{$pt}_posts_columns",			array( &$this, 'column_heading' ), 10, 1 );
			add_action( "manage_{$pt}_posts_custom_column",		array( &$this, 'column_content' ), 10, 2 );
			add_action( "manage_edit-{$pt}_sortable_columns",	array( &$this, 'column_sort' 	), 10, 2 );
		}
	}

	function get_defaults( $context = null )
	{
		/**
		 * filter 'last_modified_timestamp_defaults'
		 *
		 * @param mixed (null|string) $context  - the context the timestamp will be used in
		 */
		$defaults = apply_filters( 'last_modified_timestamp_defaults', static::$defaults, $context );

		if ( $context && isset( $defaults['contexts'][ $context ] ) )
			return wp_parse_args( $defaults['contexts'][ $context ], $defaults['base'] );
		else
			return $defaults['base'];
	}

	/**
	 * Returns a formatted timestamp as a string
	 * @param  string 	$context 		Defines what defaults are to be used to build the timestamp.
	 * @param  array  	$override 		Used by shortcode to pass per-instance values
	 * @return string 	$timestramp		timestamp html
	 */
	function construct_timestamp( $context = null, $override = null ) {
		
		if ( $override && is_array($override) )
			extract( wp_parse_args($override, $this->get_defaults()) );
		else
			extract( $this->get_defaults($context) );


		$timestamp = str_replace(
			array( '%date%','%time%','%sep%' ),												// search
			array( get_the_modified_date($datef), get_the_modified_time($timef), $sep ), 	// replace
			$format); 																		// subject

		$timestamp = '<span class="last-modified-timestamp">'.$timestamp.'</span>';

		return apply_filters( 'last_modified_timestamp_output', $timestamp, $context );
	}

	/**
	 * Shortcode handler for [last-modified] shortcode
	 * @param  array 	$atts 	Attributes array. possible attributes are 'datef', 'timef', 'sep' and 'format'.
	 *                       	All attributes are optional. Defaults can also be filtered.
	 * @return string 			timestamp html
	 */
	function shortcode_handler( $atts = array() ) {

		$atts = shortcode_atts( $this->get_defaults('shortcode'), $atts );

		return $this->construct_timestamp('shortcode', $atts);
	}

	/**
	 * Filters the admin messages at the top of the page on post.php for pages & posts to include the last modified timestamp.
	 * @param  array 	$messages
	 * @return array           		
	 */
	function modify_messages( $messages ) {

		$timestamp = $this->construct_timestamp('messages');
		
		// define a pattern to only match appropriate messages
		$match = array('updated','published','saved','submitted','restored');
		// internationalize match terms
		$match = array_map('__', $match);

		$pattern = '/' . implode('|', $match) . '/';
		
		foreach ($messages as $key => &$array) {
			foreach ($array as $inner_key => &$value) {

				if ( ! empty($value) && preg_match($pattern , $value) ) {
					
					if ( false !== $entry_point = strpos($value, '.') ) {

						$first_half = substr($value, 0, $entry_point+1 );
						$second_half = substr($value, strlen($first_half));

						$value = $first_half.' '.$timestamp.'. '.$second_half;

					} else
						$value = $timestamp.': '.$value;
				}

			}
		}
		return $messages;
	}

	// Add the Last Modified timestamp to the 'Publish' meta box in post.php
	function publish_box() {
		$timestamp = sprintf( __('Last modified on: <strong>%1$s</strong>'), $this->construct_timestamp('publish-box') );
		echo '<div class="misc-pub-section misc-pub-section-last">' . $timestamp . '</div>';
	}

	// Append the new column to the columns array
	function column_heading( $columns ) {
		$columns['last-modified'] = 'Last Modified';
		return $columns;
	}
	// Put the last modified date in the content area
	function column_content( $column_name, $id ) {
		if ( 'last-modified' == $column_name )
			echo $this->construct_timestamp('wp-table');
	}

	// Register the column as sortable
	function column_sort( $columns ) {
		$columns['last-modified'] = 'modified';
	 	return $columns;
	}

	// Output CSS for width of new column
	function print_admin_css()
	{
		echo '<style type="text/css">.fixed .column-last-modified{width:10%;}#message .last-modified-timestamp{font-weight:bold;}</style>'."\n";
	}
}

function get_the_last_modified_timestamp( $context = null, $override = null ) {

	return LastModifiedTimestamp::construct_timestamp($context,$override);

}
function the_last_modified_timestamp( $context = null, $override = null ) {

	echo get_the_last_modified_timestamp($context, $override);
}


//	MAKE IT SO.
new LastModifiedTimestamp();