<?php

namespace LastModifiedTimestamp;

class TimestampFactory
{
    /**
     * Create a new timestamp for the given named context.
     *
     * @param string $context_id
     */
    public function make($context_id = null)
    {
        $context = $this->getContext($context_id);

        return new Timestamp($context);
    }

    /**
     * Get the configuration for the given context.
     *
     * @param string $context_id
     *
     * @return array
     */
    protected function getContext($context_id)
    {
        $defaults = $this->defaults($context_id);

        return array_merge(
            isset($defaults['base']) ? $defaults['base'] : array(),
            isset($defaults['contexts'][ $context_id ]) ? $defaults['contexts'][ $context_id ] : array()
        );
    }

    /**
     * Get the default configuration.
     *
     * @param string $context_id Required for backwards compatibility with filter.
     *
     * @return array
     */
    protected function defaults($context_id = null)
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

        return apply_filters('last_modified_timestamp_defaults', $defaults, $context_id);
    }
}
