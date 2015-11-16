<?php

class LastModifiedTimestampPluginTest extends WP_UnitTestCase {

	/** @test */
	function it_is_initializable()
	{
		$this->assertTrue(class_exists('LastModifiedTimestamp'));
		$this->assertInstanceOf('LastModifiedTimestamp', LastModifiedTimestamp::get_instance());
	}

	/** @test */
	function it_registers_a_shortcode()
	{
		global $shortcode_tags;
		$this->assertTrue(isset($shortcode_tags['last-modified']));
	}

	/** @test */
	function it_exposes_two_global_template_functions()
	{
		$this->assertTrue(function_exists('get_the_last_modified_timestamp'));
		$this->assertTrue(function_exists('the_last_modified_timestamp'));
	}
}

