<?php

use LastModifiedTimestamp\TimestampFactory;

class LastModifiedTimestamp
{
    /**
     * @var LastModifiedTimestamp Plugin class instance.
     */
    protected static $instance;
    protected $factory;

    /**
     * LastModifiedTimestamp constructor.
     */
    public function __construct()
    {
        $this->factory = new TimestampFactory();
    }

    /**
     * @param LastModifiedTimestamp $instance Initialize the plugin instance.
     */
    public static function bootstrap($instance)
    {
        self::$instance = $instance;
        add_action('init', array($instance, 'init'));
    }

    /**
     * Initialize the plugin instance.
     */
    public function init()
    {
        add_shortcode('last-modified', array($this, 'shortcode_handler'));

        add_action('admin_enqueue_scripts', array($this, 'add_styles'));
        add_filter('post_updated_messages', array($this, 'modify_messages'));
        add_action('post_submitbox_misc_actions', array($this, 'publish_box'), 1);

        foreach (get_post_types() as $pt) {
            add_filter("manage_{$pt}_posts_columns", array($this, 'column_heading'));
            add_action("manage_{$pt}_posts_custom_column", array($this, 'column_content'), 10, 2);
            add_action("manage_edit-{$pt}_sortable_columns", array($this, 'column_sort'), 10, 2);
        }
    }

    /**
     * Returns a formatted timestamp as a string
     *
     * @param  string $context_id  The name of a pre-defined context
     * @param  array  $override    Overrides of context configuration
     *
     * @return string              Timestamp html
     */
    public function construct_timestamp($context_id = 'base', $override = null)
    {
        $timestamp = $this->factory->make($context_id);
        $timestamp->applyOverrides($override);

        /**
         * filter 'last_modified_timestamp_output'
         *
         * @param mixed (null|string) $context - the context the timestamp will be used in
         */
        return apply_filters('last_modified_timestamp_output', $timestamp->toHtml(), $context_id);
    }

    /**
     * Shortcode handler for [last-modified] shortcode
     *
     * @param  array $atts    Attributes array. possible attributes are 'datef', 'timef', 'sep' and 'format'.
     *                        All attributes are optional. Defaults can also be filtered.
     *
     * @return string            timestamp html
     */
    public function shortcode_handler($atts = array())
    {
        return $this->construct_timestamp('shortcode', $atts);
    }

    /**
     * Filters the admin messages at the top of the page on post.php for pages & posts to include the last modified
     * timestamp.
     *
     * @param  array $messages
     *
     * @return array
     */
    public function modify_messages($messages)
    {
        $timestamp = $this->construct_timestamp('messages');

        return array_map(function($post_type_messages) use ($timestamp) {
            return array_map(function($msg) use ($timestamp) {
                $entry_point = strpos($msg, '<a');

                // Inject the timestamp before the link in the message if it exists
                if (false !== $entry_point) {
                    $first_half  = substr($msg, 0, $entry_point - 1);
                    $second_half = substr($msg, strlen($first_half));
                    return "$first_half $timestamp. $second_half";
                }

                // Otherwise append the timestamp to the end of the message
                return "$msg $timestamp";
            }, $post_type_messages);
        }, $messages);
    }

    /**
     * Render the Last Modified timestamp within the 'Publish' meta box in post.php
     */
    public function publish_box()
    {
        $timestamp = $this->construct_timestamp('publish-box');

        include LAST_MODIFIED_TS__DIR . '/views/publish-box.php';
    }

    /**
     * Register the Last Modified column heading for post list tables.
     *
     * @param $columns
     *
     * @return mixed
     */
    public function column_heading($columns)
    {
        $columns['last-modified'] = _x('Last Modified', 'column heading', 'last-modified-timestamp');

        return $columns;
    }

    /**
     * Populate the content of the Last Modified list table column row.
     *
     * @param $column_name
     * @param $id
     */
    public function column_content($column_name, $id)
    {
        if ('last-modified' == $column_name) {
            echo $this->construct_timestamp('wp-table');
        }
    }

    /**
     * Register the Last Modified list table column as sortable by the modified date.
     *
     * @param array $columns
     *
     * @return array
     */
    public function column_sort($columns)
    {
        $columns['last-modified'] = 'modified';

        return $columns;
    }

    /**
     * Output common CSS for wp-admin.
     */
    public function add_styles()
    {
        $value_selectors = array_reduce(array('author', 'date', 'time'), function($selector, $attribute) {
            $selector .= $selector ? ', ' : '';
            return $selector . ".notice .last-modified-timestamp--$attribute";
        }, '');

        wp_add_inline_style('list-tables', '.fixed .column-last-modified { width: 10em; }');
        wp_add_inline_style('edit', "$value_selectors { font-weight: bold; }");
    }

    /**
     * Get the plugin class instance.
     *
     * @return LastModifiedTimestamp
     */
    public static function get_instance()
    {
        return self::$instance;
    }
}
