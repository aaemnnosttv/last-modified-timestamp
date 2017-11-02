<?php

use LastModifiedTimestamp\Context;

class ContextTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    function can_get_a_configuration_value_from_the_context()
    {
        $context = new Context();

        $this->assertSame('M j, Y', $context->get('datef'));
        $this->assertSame('some default', $context->get('non-existent', 'some default'));
    }

    /** @test */
    function can_accept_an_array_of_overrides()
    {
        $context = new Context();
        $this->assertSame('M j, Y', $context->get('datef'));

        $context->merge(array(
            'datef' => 'foo'
        ));

        $this->assertSame('foo', $context->get('datef'));
    }

    /** @test */
    function can_generate_a_timestamp()
    {
        $context = new Context();

        $timestamp = $context->render_timestamp();

        $this->assertNotEmpty($timestamp);
    }

    /** @test */
    function contextual_defaults_take_precedence_over_base()
    {
        $base_context = new Context();
        $messages_context = new Context('messages');

        $this->assertNotContains('%author%', $base_context->get('format'));
        $this->assertContains('%author%', $messages_context->get('format'));
    }
}
