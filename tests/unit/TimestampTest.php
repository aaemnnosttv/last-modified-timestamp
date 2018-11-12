<?php

use LastModifiedTimestamp\Timestamp;

class TimestampTest extends WP_UnitTestCase
{
    /** @test */
    function it_takes_an_array_of_contextual_configuration()
    {
        $ts = new Timestamp(array(
            'format' => 'the format',
        ));

        $this->assertSame('the format', $ts->get('format'));
    }

    /** @test */
    function it_can_merge_an_array_into_its_configuration()
    {
        $ts = new Timestamp(array(
            'format' => 'the format',
        ));

        $ts->applyOverrides(array(
            'format' => 'different',
        ));

        $this->assertSame('different', $ts->get('format'));
    }

    /** @test */
    function it_can_compile_the_formatted_string()
    {
        $post             = $this->factory()->post->create_and_get();
        $post_modified_ts = strtotime($post->post_modified_gmt);

        $ts = new Timestamp(array(
            'format' => '%date% %sep% %time%',
            'datef'  => 'U',
            'timef'  => 'U',
            'sep'    => '||',
        ));

        $expected = '<span class="last-modified-timestamp--date">' . $post_modified_ts . '</span>';
        $expected .= ' <span class="last-modified-timestamp--sep">||</span> ';
        $expected .= '<span class="last-modified-timestamp--time">' . $post_modified_ts . '</span>';

        $this->assertEquals($expected, $ts->compile($post));
    }
}
