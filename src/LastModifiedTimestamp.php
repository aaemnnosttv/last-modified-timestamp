<?php

class LastModifiedTimestamp
{
    /**
     * @var LastModifiedTimestamp
     */
    private static $instance;

    /**
     * @param LastModifiedTimestamp $instance
     */
    public static function bootstrap($instance)
    {
        add_action('init', array($instance, 'init'));
        self::$instance = $instance;
    }

    /**
     * Initialize the plugin instance.
     */
    public function init()
    {
        load_plugin_textdomain(
            'last-modified-timestamp',
            false,
            dirname(plugin_basename(LAST_MODIFIED_TS__DIR)) . '/languages/'
        );

        add_shortcode('last-modified', array($this, 'shortcode_handler'));

        add_action('admin_print_styles-edit.php', array($this, 'print_admin_css'));
        add_action('admin_print_styles-post.php', array($this, 'print_admin_css'));
        add_action('admin_print_styles-post-new.php', array($this, 'print_admin_css'));
        add_action('post_submitbox_misc_actions', array($this, 'publish_box'), 1);

        add_filter('post_updated_messages', array($this, 'modify_messages'));

        foreach (get_post_types() as $pt) {
            add_filter("manage_{$pt}_posts_columns", array($this, 'column_heading'), 10, 1);
            add_action("manage_{$pt}_posts_custom_column", array($this, 'column_content'), 10, 2);
            add_action("manage_edit-{$pt}_sortable_columns", array($this, 'column_sort'), 10, 2);
        }
    }

    /**
     * Returns a formatted timestamp as a string
     *
     * @param  string $context  Defines what defaults are to be used to build the timestamp.
     * @param  array  $override Used by shortcode to pass per-instance values
     *
     * @return string    $timestramp        timestamp html
     */
    function construct_timestamp($context = null, $override = null)
    {
        $data = new LastModified__Context($context);

        if ($override && is_array($override)) {
            $data->merge($override);
        }

        $timestamp = '<span class="last-modified-timestamp">' . $data->render_timestamp() . '</span>';

        /**
         * filter 'last_modified_timestamp_output'
         *
         * @param mixed (null|string) $context - the context the timestamp will be used in
         */
        return apply_filters('last_modified_timestamp_output', $timestamp, $context);
    }

    /**
     * Shortcode handler for [last-modified] shortcode
     *
     * @param  array $atts    Attributes array. possible attributes are 'datef', 'timef', 'sep' and 'format'.
     *                        All attributes are optional. Defaults can also be filtered.
     *
     * @return string            timestamp html
     */
    function shortcode_handler($atts = array())
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
    function modify_messages($messages)
    {
        $timestamp = $this->construct_timestamp('messages');

        foreach ($messages as $posttype => &$array) {
            foreach ($array as $index => &$msg) {
                if (false !== $entry_point = strpos($msg, '.')) {
                    $first_half  = substr($msg, 0, $entry_point + 1);
                    $second_half = substr($msg, strlen($first_half));
                    $msg         = "$first_half $timestamp. $second_half";
                } else {
                    $msg = "$timestamp: $msg";
                }
            }
        }

        return $messages;
    }

    /**
     * Add the Last Modified timestamp to the 'Publish' meta box in post.php
     */
    function publish_box()
    {
        $timestamp = sprintf(
            __('Last modified on: <strong>%1$s</strong>', 'last-modified-timestamp'),
            $this->construct_timestamp('publish-box')
        );
        echo '<div class="misc-pub-section misc-pub-section-last">' . $timestamp . '</div>';
    }

    /**
     * Register the Last Modified column heading for post list tables.
     *
     * @param $columns
     *
     * @return mixed
     */
    function column_heading($columns)
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
    function column_content($column_name, $id)
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
    function column_sort($columns)
    {
        $columns['last-modified'] = 'modified';

        return $columns;
    }

    /**
     * Output common CSS for wp-admin.
     */
    function print_admin_css()
    {
        echo '<style type="text/css">.fixed .column-last-modified{width:10%;}#message .last-modified-timestamp{font-weight:bold;}</style>' . "\n";
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
