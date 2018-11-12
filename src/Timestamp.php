<?php

namespace LastModifiedTimestamp;

class Timestamp
{
    /**
     * @var array
     */
    protected $context;

    /**
     * Timestamp constructor.
     *
     * @param array $context
     */
    public function __construct($context = [])
    {
        $this->context = $context;
    }

    /**
     * Merge configuration data into the context.
     *
     * @param array $config
     */
    public function applyOverrides($config)
    {
        if (is_array($config)) {
            $this->context = array_merge($this->context, $config);
        }
    }

    /**
     * Generate the formatted string using the replacement from the configuration.
     *
     * @param int|\WP_Post|null $post
     *
     * @return string
     */
    public function compile($post = null)
    {
        if (null === $post) {
            $post = get_post();
        }

        $placeholders = array(
            'author' => $this->get_the_modified_author($post ?: new \WP_Post(new \stdClass())),
            'date' => get_the_modified_date($this->get('datef'), $post),
            'time' => get_the_modified_time($this->get('timef'), $post),
            'sep'  => $this->get('sep'),
        );

        $replacements = array_map(function($value, $key) {
            return sprintf('<span class="last-modified-timestamp--%s">%s</span>', $key, $value);
        }, $placeholders, array_keys($placeholders));

        return str_replace(
            $this->formatPlaceholders(array_keys($placeholders)),
            array_combine(array_keys($placeholders), $replacements),
            $this->get('format')
        );
    }

    protected function get_the_modified_author($post) {
        if ($last_id = get_post_meta(get_post_field('ID', $post, 'raw'), '_edit_last', true)) {
            $last_user = get_userdata($last_id);

            /**
             * This filter is documented in wp-includes/author-template.php
             */
            return apply_filters('the_modified_author', $last_user->display_name);
        }

        return '';
    }

    /**
     * Transform an array of placeholder names into placeholders for replacements.
     *
     * @param array $names
     *
     * @return array
     */
    protected function formatPlaceholders($names)
    {
        return array_map(function($name) {
            return '%' . $name . '%';
        }, $names);
    }

    /**
     * Get a configuration value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return array_key_exists($key, $this->context) ? $this->context[$key] : null;
    }

    /**
     * Get the timestamp as HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        return '<span class="last-modified-timestamp">' . $this->compile() . '</span>';
    }
}
