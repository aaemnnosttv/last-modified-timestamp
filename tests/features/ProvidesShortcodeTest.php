<?php

class ProvidesShortcodeTest extends WP_UnitTestCase
{
    /** @test */
    function shortcode_returns_last_modified_timestamp()
    {
        $this->assertShortcodeContains('last-modified-timestamp');
    }

    /** @test */
    function shortcode_can_accept_a_custom_format()
    {
        $this->assertShortcodeContains('custom format', array(
            'format' => 'custom format',
        ));
    }

    /** @test */
    function shortcode_can_accept_a_custom_date_format()
    {
        $this->new_global_post();
        $expected = (string) get_the_modified_date('U');

        $this->assertShortcodeContains($expected, array(
            'datef' => 'U',
            'format' => '%date%'
        ));
    }

    /** @test */
    function shortcode_can_accept_a_custom_time_format()
    {
        $this->new_global_post();
        $expected = (string) get_the_modified_time('U');

        $this->assertShortcodeContains($expected, array(
            'timef' => 'U',
            'format' => '%time%'
        ));
    }

    /** @test */
    function shortcode_can_accept_a_custom_separator()
    {
        $this->assertShortcodeContains(
            '--separator--',
            array(
                'sep' => '--separator--',
                'format' => '__START__%sep%__END__'
            )
        );
    }

    /** @test */
    function shortcode_can_include_the_author()
    {
        $post = $this->new_global_post();
        $author = $this->factory()->user->create_and_get(array('display_name' => 'The Name'));
        $this->edit_post($post, $author);

        $this->assertShortcodeContains('The Name', array(
            'format' => '%author%',
        ));
    }

    protected function new_global_post($args = array())
    {
        $GLOBALS['post'] = $this->factory()->post->create_and_get($args);

        return $GLOBALS['post'];
    }

    protected function edit_post($post, $user = null)
    {
        $post_id = get_post_field('ID', $post) ?: $post;
        $user_id = $user instanceof WP_User ? $user->ID : $user;

        update_post_meta($post_id, '_edit_last', (int) $user_id);
    }

    /**
     * Call the shortcode directly and assert the return value contains a given string.
     *
     * @param       $string
     * @param array $atts
     */
    protected function assertShortcodeContains($string, $atts = array(), $content = '')
    {
        global $shortcode_tags;
        $output = call_user_func($shortcode_tags['last-modified'], $atts, $content, 'last-modified');

        $this->assertContains($string, $output,
            'Failed to assert the shortcode output contains the given string.'
        );
    }
}
