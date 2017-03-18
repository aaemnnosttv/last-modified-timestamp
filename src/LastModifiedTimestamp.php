<?php

class LastModifiedTimestamp
{
	private static $instance;

	public function __construct()
	{
		$this->defaults = array(
			// base defaults
			'base' => array(
				'datef'  => _x('M j, Y', 'default date format', 'last-modified-timestamp'),
				'timef'  => null,
				'sep'    => _x('@', 'default separator', 'last-modified-timestamp'),
				'format' => _x('%date% %sep% %time%', 'default format', 'last-modified-timestamp')
			),
			// extended contextual defaults
			'contexts' => array(
				'messages'    	=> array(
					'datef' => _x('M j, Y', 'messages date format', 'last-modified-timestamp'),
					'sep'   => _x('@', 'messages separator', 'last-modified-timestamp')
				),
				'publish-box' 	=> array(
					'datef' => _x('M j, Y', 'publish-box date format', 'last-modified-timestamp'),
					'sep'   => _x('@', 'publish-box separator', 'last-modified-timestamp')
				),
				'shortcode' 	=> array(
					'datef' => _x('M j, Y', 'shortcode date format', 'last-modified-timestamp'),
					'sep'   => _x('@', 'shortcode separator', 'last-modified-timestamp')
				),
				'wp-table'    	=> array(
					'datef' => _x('Y/m/d', 'wp-table date format', 'last-modified-timestamp'),
					'sep'   => _x('<br />', 'wp-table separator', 'last-modified-timestamp')
				),
			)
		);
	}

	/**
	 * @param LastModifiedTimestamp $instance
	 */
	public static function bootstrap($instance)
	{
		add_action('init', array($instance, 'init'));
		self::$instance = $instance;
	}

	public function init()
	{
		load_plugin_textdomain( 'last-modified-timestamp', false, dirname( plugin_basename(LAST_MODIFIED_TS__DIR) ) . '/languages/' );

		add_shortcode('last-modified', array($this, 'shortcode_handler'));

		add_action( 'admin_print_styles-edit.php',			array( $this, 'print_admin_css' ) );
		add_action( 'admin_print_styles-post.php',			array( $this, 'print_admin_css' ) );
		add_action( 'admin_print_styles-post-new.php',		array( $this, 'print_admin_css' ) );
		add_action( 'post_submitbox_misc_actions',			array( $this, 'publish_box' ), 1 );  // NEW PRIORITY

		add_filter( 'post_updated_messages',				array( $this, 'modify_messages' ) );

		foreach ( get_post_types() as $pt )
		{
			add_filter( "manage_{$pt}_posts_columns",			array( $this, 'column_heading' ), 10, 1 );
			add_action( "manage_{$pt}_posts_custom_column",		array( $this, 'column_content' ), 10, 2 );
			add_action( "manage_edit-{$pt}_sortable_columns",	array( $this, 'column_sort'    ), 10, 2 );
		}
	}

	function get_defaults( $context = null )
	{
		/**
		 * filter 'last_modified_timestamp_defaults'
		 *
		 * @param mixed (null|string) $context  - the context the timestamp will be used in
		 */
		$defaults = apply_filters( 'last_modified_timestamp_defaults', $this->defaults, $context );

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
	function construct_timestamp( $context = null, $override = null )
	{
		$data = $this->get_defaults( $context );

		if ( $override && is_array( $override ) )
			$data = wp_parse_args( $override, $data );

		extract( $data );

		$timestamp = str_replace(
			array( '%date%','%time%','%sep%' ),													// search
			array( get_the_modified_date( $datef ), get_the_modified_time( $timef ), $sep ),	// replace
			$format 																			// subject
		);

		$timestamp = '<span class="last-modified-timestamp">' . $timestamp . '</span>';

		/**
		 * filter 'last_modified_timestamp_output'
		 *
		 * @param mixed (null|string) $context  - the context the timestamp will be used in
		 */
		return apply_filters( 'last_modified_timestamp_output', $timestamp, $context );
	}

	/**
	 * Shortcode handler for [last-modified] shortcode
	 * @param  array 	$atts 	Attributes array. possible attributes are 'datef', 'timef', 'sep' and 'format'.
	 *                       	All attributes are optional. Defaults can also be filtered.
	 * @return string 			timestamp html
	 */
	function shortcode_handler( $atts = array() )
	{
		$atts = shortcode_atts( $this->get_defaults('shortcode'), $atts );
		return $this->construct_timestamp('shortcode', $atts);
	}

	/**
	 * Filters the admin messages at the top of the page on post.php for pages & posts to include the last modified timestamp.
	 * @param  array 	$messages
	 * @return array
	 */
	function modify_messages( $messages )
	{
		$timestamp = $this->construct_timestamp('messages');

		foreach ( $messages as $posttype => &$array )
		{
			foreach ( $array as $index => &$msg )
			{
				if ( false !== $entry_point = strpos( $msg, '.' ) )
				{
					$first_half  = substr( $msg, 0, $entry_point+1 );
					$second_half = substr( $msg, strlen( $first_half ));
					$msg       = "$first_half $timestamp. $second_half";
				}
				else
					$msg = "$timestamp: $msg";
			}
		}

		return $messages;
	}

	// Add the Last Modified timestamp to the 'Publish' meta box in post.php
	function publish_box()
	{
		$timestamp = sprintf( __('Last modified on: <strong>%1$s</strong>', 'last-modified-timestamp'), $this->construct_timestamp('publish-box') );
		echo '<div class="misc-pub-section misc-pub-section-last">' . $timestamp . '</div>';
	}

	// Append the new column to the columns array
	function column_heading( $columns )
	{
		$columns['last-modified'] = _x('Last Modified', 'column heading', 'last-modified-timestamp');
		return $columns;
	}

	// Put the last modified date in the content area
	function column_content( $column_name, $id )
	{
		if ( 'last-modified' == $column_name )
			echo $this->construct_timestamp('wp-table');
	}

	// Register the column as sortable
	function column_sort( $columns )
	{
		$columns['last-modified'] = 'modified';
		return $columns;
	}

	// Output CSS for width of new column
	function print_admin_css()
	{
		echo '<style type="text/css">.fixed .column-last-modified{width:10%;}#message .last-modified-timestamp{font-weight:bold;}</style>'."\n";
	}

	public static function get_instance()
	{
		return self::$instance;
	}

}