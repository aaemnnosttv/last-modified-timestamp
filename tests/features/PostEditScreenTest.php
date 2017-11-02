<?php

class PostEditScreenTest extends WP_UnitTestCase
{
	/** @test */
	function adds_timestamp_to_post_updated_messages()
	{
		$m = array();
		$m['post'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __('Post updated.'),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('Post updated.'),
			/* translators: %s: date and time of the revision */
//			5 => isset($_GET['revision']) ? sprintf(__('Post restored to revision from %s.'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
			6 => __('Post published.'),
			7 => __('Post saved.'),
			8 => __('Post submitted.'),
//			9 => sprintf(__('Post scheduled for: %s.'), '<strong>' . $scheduled_date . '</strong>') . $scheduled_post_link_html,
			10 => __('Post draft updated.'),
		);
		$messages = apply_filters('post_updated_messages', $m);

		foreach ($messages['post'] as $message) {
			$this->assertContains('last-modified-timestamp', $message);
		}
	}

	/** @test */
	function adds_last_modified_to_post_publish_box()
	{
		ob_start();
		do_action('post_submitbox_misc_actions', $this->factory->post->create_and_get());
		$output = ob_get_clean();

		$this->assertContains('last-modified-timestamp', $output);
	}

}
