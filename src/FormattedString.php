<?php

namespace LastModifiedTimestamp;

class FormattedString
{
    protected $template;

    /**
     * Timestamp constructor.
     *
     * @param string $template
     */
    public function __construct($template)
    {
        $this->template = $template;
    }

    /**
     * @param array $replacements
     *
     * @return string
     */
    public function render($replacements = array())
    {
        return str_replace(
            $this->get_placeholders(array_keys($replacements)),
            $replacements,
            $this->template
        );
    }

    /**
     * @param array $array_keys
     *
     * @return array
     */
    protected function get_placeholders($array_keys)
    {
        $placeholders = array();

        foreach ($array_keys as $key) {
            $placeholders[] = '%' . $key . '%';
        }

        return $placeholders;
    }
}
