<?php

class LastModified__Context
{
    /* @var string */
    protected $context;
    /* @var array */
    protected $defaults;

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
     * @param array $data
     */
    public function merge($data)
    {
        $this->data = array_merge(
            (array) $this->data,
            (array) $data
        );
    }

    /**
     * @return string
     */
    public function render_timestamp()
    {
        $timestamp = new LastModified__FormattedString($this->get('format'));

        return $timestamp->render(array(
            'author' => get_post() ? get_the_modified_author() : '',
            'date' => get_the_modified_date($this->get('datef')),
            'time' => get_the_modified_time($this->get('timef')),
            'sep'  => $this->get('sep'),
        ));
    }

    public function get($key, $default = null)
    {
        if (isset($this->data[ $key ])) {
            return $this->data[ $key ];
        }

        return $default;
    }

    /**
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
                ),
                'shortcode'   => array(
                    'datef' => _x('M j, Y', 'shortcode date format', 'last-modified-timestamp'),
                    'sep'   => _x('@', 'shortcode separator', 'last-modified-timestamp'),
                ),
                'wp-table'    => array(
                    'datef' => _x('Y/m/d', 'wp-table date format', 'last-modified-timestamp'),
                    'sep'   => _x('<br />', 'wp-table separator', 'last-modified-timestamp'),
                ),
            ),
        );

        return $defaults;
    }
}
