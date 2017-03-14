<?php

class AdminListTableColumnTest extends WP_UnitTestCase
{
	/**
	 * @test
	 * @dataProvider provide_all_post_types
	 */
	function adds_last_modified_column_to_all_post_type_list_tables($post_type)
	{
		$post_columns = apply_filters("manage_{$post_type}_posts_columns", array());

		$this->assertArrayHasKey('last-modified', $post_columns);
	}

	/**
	 * @test
	 * @dataProvider provide_all_post_types
	 */
	function outputs_timestamp_in_last_modified_column($post_type)
	{
		ob_start();
		$post_id = $this->factory->post->create(array('post_type' => $post_type));
		do_action("manage_{$post_type}_posts_custom_column", 'last-modified', $post_id);
		$output = ob_get_clean();

		$this->assertContains('last-modified-timestamp', $output);
	}

	/**
	 * @test
	 * @dataProvider provide_all_post_types
	 */
	function registers_the_last_modified_column_as_sortable($post_type)
	{
		$sortable = apply_filters("manage_edit-{$post_type}_sortable_columns", array());

		$this->assertArrayHasKey('last-modified', $sortable);
	}

	function provide_all_post_types()
	{
		return array_map(function($name) {
			return (array) $name;
		}, get_post_types());
	}
}