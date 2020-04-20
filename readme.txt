=== Last Modified Timestamp ===
Stable tag: 1.0.5
Contributors: aaemnnosttv
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LRA4JZYALHX82
Tags: page modified, post modified, timestamp, last modified, modified time, custom post type modified
Requires at least: 3.2.0
Requires PHP: 5.3
Tested up to: 5.4
License: GPLv2 or later

Adds the last modified time to the admin interface as well as a [last-modified] shortcode to use on the front-end.

== Description ==

This plugin adds information to the admin interface about when each post/page was last modified (including custom post types!).

Enhanced areas:

1. Page/post admin tables - added `Last Modified` column which is also sortable.
1. Page/post edit screen (`post.php`) - added `Last modified on: *timestamp*` to `Publish` meta box.
1. Admin messages after editing a page/post - ie: `Post updated. *timestamp* View Post`,

No options currently available, but the output can be fully customized with filters and the shortcode can be easily customized using attributes!

### Gutenberg, WordPress 5, and Beyond

This plugin does not yet enhance the new editor provided by Gutenberg and introduced as the default editor in WordPress 5.0. No plans exist to add support for this although it may be added in the future.
Other areas of wp-admin enhanced by the plugin still work, as does the classic editor.

== Installation ==

1. Upload the `last-modified-timestamp` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.


== Frequently Asked Questions ==

**How to add the last modified time to my page or post?**

This plugin does not change the public facing appearance of your website, but gives you a few ways to add this if you wish.

1. Using the `[last-modified]` shortcode. See below.
2. Using template functions in your theme or plugin. See below.

**How to provide the last modified time to search engines?**

The best way to provide the last modified timestamp to search engines is by using a plugin to add an XML sitemap for your website. This is a special kind of document which provides various information to search engines about all the content on your website, including when each was last modified. Many SEO plugins provide include this functionality with them, but there are many standalone plugins for this as well. This allows search engines to reference a single file (which is automatically kept up to date for you by the plugin) to know exactly what content has changed on your whole website since it was last indexed, rather than recrawling every page.

This plugin may be used to display the last modified date and time to a reader on your website, but it is not intended as a tool for SEO.

**How to use the [last-modified] shortcode?**

[last-modified] Returns the last modified timestamp in this format `date seperator time`.

_Attributes (all optional)_

datef - specify a date format using the [PHP date format](http://www.php.net/manual/en/function.date.php).

timef - specify a time format using the [PHP date format](http://www.php.net/manual/en/function.date.php).

sep   - specify the character/text you want to use to separate the date & time.

format - define the output format using placeholders `%date%`, `%time%`, and `%sep%`.  Other text can be used as well.



**How to change the outputted date/time format?**

By default, the plugin mimicks the time & date formats used in the same context (ie: admin tables, publish box) that WordPress uses.

As mentioned above, LMT uses PHP date format strings for the formatting of the outputted date & time.

To customize the output with a shortcode, use the attributes as described above.

To customize the output in an admin context, a filter may be used.

* **last_modified_timestamp_defaults** - allows default values to be filtered. Shortcode attributes override defaults when present, otherwise there are defaults for shortcode output as well.  Passes 1 parameter (array).

For example, if you wanted to change the time format in the admin messages that appear after a post is modified to a 24hr format with leading zeros, add this to your theme's functions.php:

`function my_lmt_defaults( $d ) {

	$d['contexts']['messages']['timef'] = 'H:i';

	return $d;
}
add_filter('last_modified_timestamp_defaults','my_lmt_defaults');`

**Template Tags**

Models the function naming convention used by WordPress for `get_the_content` / `the_content` and similar functions.

* `get_the_last_modified_timestamp()` - returns timestamp.
* `the_last_modified_timestamp()` - displays/echos the timestamp.

These functions accept 2 arguments, both are optional:

* `$context` (string) to output formatted according to a defined context (ie: admin messages, posts table, etc.)
* `$override` (array) using this will override any defaults that are specified here, but output can still be overriden at final output.
Example array structure is: `array('datef' => 'M j, Y', 'timef' => 'g:i', 'sep' => '&rarr;', 'format' => '%date% %sep% %time%')`



== Screenshots ==

1. Page/post admin tables - added `Last Modified` column.
1. Page/post edit screen (`post.php`) - added `Last modified on: *timestamp*` to `Publish` meta box.
1. Admin messages after editing a page/post - ie: `Post updated. *timestamp* View Post`

== Changelog ==

= 1.0.5 =
* Tweaked hook for testing
* Integrated GitHub Actions

= 1.0.4 =
* Add automated tests

= 1.0.3 =
* Template function bugfix

= 1.0.2 =
* Min required WP bump to >= 3.2
* PHP compatibility fix

= 1.0.1 =
* General housekeeping & maintanence
* Tested against 3.8

= 1.0 =
**Major Update**

* Added support for all custom post types.
* Added `[last-modified]` shortcode.
* Added filters to provide complete control.
* Added template tags.
* Encapsulated code.

= 0.4 =
* Added support for other types of update messages.
* Added filter to allow output to be customized.

= 0.3.1 =
* Fixed sortable column on pages table.

= 0.3 =
* The `Last Modified` column in the admin post/page tables is now sortable!
* CSS - widened `Last Modified` column to account for extra width needed for sortable arrow.
* Updated screenshot of `Last Modified` column in the admin post/page tables.
* Corrected a typo in the admin messages for pages.

= 0.2 =
* Fixed date formatting in the admin tables.

= 0.1 =
* Initial release