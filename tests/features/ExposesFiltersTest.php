<?php

class ExposesFiltersTest extends WP_UnitTestCase
{
	/** @test */
	function allows_defaults_to_be_filtered()
	{
		$called = false;

		add_filter('last_modified_timestamp_defaults', function ($all_defaults, $context) use (&$called) {
			$called = true;

			$this->assertTrue(isset($all_defaults['base']));
			$this->assertTrue(isset($all_defaults['contexts']['messages']));
			$this->assertTrue(isset($all_defaults['contexts']['publish-box']));
			$this->assertTrue(isset($all_defaults['contexts']['shortcode']));
			$this->assertTrue(isset($all_defaults['contexts']['wp-table']));
			$this->assertSame('some-context', $context);

			return $all_defaults;
		}, 10, 2);

		get_the_last_modified_timestamp('some-context');

		if (! $called) {
			$this->fail();
		}
	}

	/** @test */
	function allows_output_to_be_filtered()
	{
		$called = false;

		add_filter('last_modified_timestamp_output', function ($timestamp, $context) use (&$called) {
			$called = true;

			$this->assertContains('last-modified-timestamp', $timestamp);
			$this->assertSame('some-context', $context);

			return $timestamp;
		}, 10, 2);

		get_the_last_modified_timestamp('some-context');

		if (! $called) {
			$this->fail();
		}
	}

}