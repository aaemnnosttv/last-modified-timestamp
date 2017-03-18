<?php


class LastModifiedContextTest extends PHPUnit_Framework_TestCase
{
	/** @test */
	function can_get_a_configuration_value_from_the_context()
	{
		$context = new LastModified__Context;

		$this->assertSame('M j, Y', $context->get('datef'));
		$this->assertSame('some default', $context->get('non-existent', 'some default'));
	}

	/** @test */
	function can_accept_an_array_of_overrides()
	{
		$context = new LastModified__Context;
		$this->assertSame('M j, Y', $context->get('datef'));

		$context->merge(array(
			'datef' => 'foo'
		));

		$this->assertSame('foo', $context->get('datef'));
	}

	/** @test */
	function can_generate_a_timestamp()
	{
		$context = new LastModified__Context;

		$timestamp = $context->render_timestamp();

		$this->assertNotEmpty($timestamp);
	}

}
