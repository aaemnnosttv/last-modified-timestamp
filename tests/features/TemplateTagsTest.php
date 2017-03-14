<?php

class TemplateTagsTest extends WP_UnitTestCase
{
	/** @test */
	function can_get_the_last_modified_timestamp_html_with_a_function()
	{
		$output = get_the_last_modified_timestamp();

		$this->assertContains('last-modified-timestamp', $output);
	}

	/** @test */
	function can_output_the_last_modified_timestamp_html_with_a_function()
	{
		ob_start();
		the_last_modified_timestamp();
		$output = ob_get_clean();

		$this->assertContains('last-modified-timestamp', $output);
	}
}