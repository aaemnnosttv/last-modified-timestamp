<?php

use LastModifiedTimestamp\FormattedString;

class FormattedStringTest extends PHPUnit_Framework_TestCase
{
	/** @test */
	function can_render_placeholders_in_a_template_string_to_values()
	{
		$string = new FormattedString('%greeting%, %name%.');

		$rendered = $string->render(array(
			'greeting' => 'Hello',
			'name' => 'Dave',
		));

		$this->assertSame('Hello, Dave.', $rendered);
	}
}
