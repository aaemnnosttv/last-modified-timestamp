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
		global $post;
		$post = $this->factory->post->create_and_get();
		$expected = (string) get_the_modified_date('U');

		$this->assertShortcodeContains($expected, array(
			'datef' => 'U',
			'format' => '%date%'
		));
	}

	/** @test */
	function shortcode_can_accept_a_custom_time_format()
	{
		global $post;
		$post = $this->factory->post->create_and_get();
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
			'__START__--separator--__END__',
			array(
				'sep' => '--separator--',
				'format' => '__START__%sep%__END__'
			)
		);
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