<?php

class ExposesFiltersTest extends WP_UnitTestCase
{
	/** @test */
	function allows_defaults_to_be_filtered()
	{
		$test_case = $this;
		$called = false;

		add_filter('last_modified_timestamp_defaults', function ($all_defaults, $context) use ($test_case, &$called) {
			$called = true;

			$test_case->assertTrue(isset($all_defaults['base']));
			$test_case->assertTrue(isset($all_defaults['contexts']['messages']));
			$test_case->assertTrue(isset($all_defaults['contexts']['publish-box']));
			$test_case->assertTrue(isset($all_defaults['contexts']['shortcode']));
			$test_case->assertTrue(isset($all_defaults['contexts']['wp-table']));
			$test_case->assertSame('some-context', $context);

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
		$test_case = $this;
		$called = false;

		add_filter('last_modified_timestamp_output', function ($timestamp, $context) use ($test_case, &$called) {
			$called = true;

			$test_case->assertContains('last-modified-timestamp', $timestamp);
			$test_case->assertSame('some-context', $context);

			return $timestamp;
		}, 10, 2);

		get_the_last_modified_timestamp('some-context');

		if (! $called) {
			$this->fail();
		}
	}

}