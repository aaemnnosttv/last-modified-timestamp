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
        $defaults      = apply_filters('last_modified_timestamp_defaults', $this->get_defaults(), $context);
        $this->data    = array_merge(
            isset($defaults['base']) ? $defaults['base'] : array(),
            isset($defaults[ $context ]) ? $defaults[ $context ] : array()
        );
    }

    /**
     * @param array $data
     */
    public function merge($data)
    {
        $this->data = array_merge(
            $this->data,
            $data
        );
    }

    /**
     * @return string
     */
    public function render_timestamp()
    {
        $timestamp = new LastModified__FormattedString($this->get('format'));

        return $timestamp->render(array(
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
        $defaults = [
            // base defaults
            'base'     => [
                'datef'  => _x('M j, Y', 'default date format', 'last-modified-timestamp'),
                'timef'  => null,
                'sep'    => _x('@', 'default separator', 'last-modified-timestamp'),
                'format' => _x('%date% %sep% %time%', 'default format', 'last-modified-timestamp'),
            ],
            // extended contextual defaults
            'contexts' => [
                'messages'    => [
                    'datef' => _x('M j, Y', 'messages date format', 'last-modified-timestamp'),
                    'sep'   => _x('@', 'messages separator', 'last-modified-timestamp'),
                ],
                'publish-box' => [
                    'datef' => _x('M j, Y', 'publish-box date format', 'last-modified-timestamp'),
                    'sep'   => _x('@', 'publish-box separator', 'last-modified-timestamp'),
                ],
                'shortcode'   => [
                    'datef' => _x('M j, Y', 'shortcode date format', 'last-modified-timestamp'),
                    'sep'   => _x('@', 'shortcode separator', 'last-modified-timestamp'),
                ],
                'wp-table'    => [
                    'datef' => _x('Y/m/d', 'wp-table date format', 'last-modified-timestamp'),
                    'sep'   => _x('<br />', 'wp-table separator', 'last-modified-timestamp'),
                ],
            ],
        ];

        return $defaults;
    }
}
