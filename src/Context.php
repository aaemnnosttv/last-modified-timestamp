<?php

namespace LastModifiedTimestamp;

class Context
{
    /**
     * @var string The named context id.
     */
    protected $context;

    /**
     * @var array The contextual configuration.
     */
    protected $config;

    /**
     * LastModifiedContext constructor.
     *
     * @param string $context
     */
    public function __construct($context = 'base')
    {
        $this->context = $context;
        $this->populate_data();
    }

    /**
     * Populate the configuration for the given context.
     */
    protected function populate_data()
    {
        $defaults = $this->get_defaults();
        $this->merge(isset($defaults['base']) ? $defaults['base'] : array());
        $this->merge(isset($defaults['contexts'][ $this->context ]) ? $defaults['contexts'][ $this->context ] : array());
    }

    /**
     * Merge the given data with the configuration for this context.
     *
     * @param array $data
     */
    public function merge($data)
    {
        $this->config = array_merge(
            (array) $this->config,
            (array) $data
        );
    }

    /**
     * Render the HTML for the timestamp.
     *
     * @return string
     */
    public function render_timestamp()
    {
        $timestamp = new FormattedString($this->get('format'));

        $placeholders = array(
            'author' => get_post() ? get_the_modified_author() : '',
            'date' => get_the_modified_date($this->get('datef')),
            'time' => get_the_modified_time($this->get('timef')),
            'sep'  => $this->get('sep'),
        );

        $replacements = array_map(function($value, $key) {
            return sprintf('<span class="last-modified-timestamp--%s">%s</span>', $key, $value);
        }, $placeholders, array_keys($placeholders));

        $replacements = array_combine(array_keys($placeholders), $replacements);

        return $timestamp->render($replacements);
    }

    /**
     * Get a named configuration value.
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (isset($this->config[ $key ])) {
            return $this->config[ $key ];
        }

        return $default;
    }

    /**
     * Get the default configuration.
     *
     * @return array
     */
    protected function get_defaults()
    {
        $defaults = array(
            // base defaults
            'base'     => array(
                'datef'  => _x('M j, Y', 'default date format', 'last-modified-timestamp'),
                'timef'  => null,
                'sep'    => _x('@', 'default separator', 'last-modified-timestamp'),
                'format' => _x('%date% %sep% %time%', 'default format', 'last-modified-timestamp'),
            ),
            // extended contextual defaults
            'contexts' => array(
                'messages'    => array(
                    'datef' => _x('M j, Y', 'messages date format', 'last-modified-timestamp'),
                    'sep'   => _x('@', 'messages separator', 'last-modified-timestamp'),
                    'format' => _x('Last modified by %author% on %date% %sep% %time%', 'messages format', 'last-modified-timestamp'),
                ),
                'publish-box' => array(
                    'datef' => _x('M j, Y', 'publish-box date format', 'last-modified-timestamp'),
                    'sep'   => _x('@', 'publish-box separator', 'last-modified-timestamp'),
                    'format' => _x('Last modified on: <strong>%date% %sep% %time%</strong>', 'publish-box format', 'last-modified-timestamp'),
                ),
                'shortcode'   => array(
                    'datef' => _x('M j, Y', 'shortcode date format', 'last-modified-timestamp'),
                    'sep'   => _x('@', 'shortcode separator', 'last-modified-timestamp'),
                ),
                'wp-table'    => array(
                    'datef' => _x('Y/m/d', 'wp-table date format', 'last-modified-timestamp'),
                    'sep'   => _x('<br />', 'wp-table separator', 'last-modified-timestamp'),
                    'format' => _x('%time% %sep% <abbr title>%date%</abbr>', 'wp-table format', 'last-modified-timestamp'),
                ),
            ),
        );

        return apply_filters('last_modified_timestamp_defaults', $defaults, $this->context);
    }
}
