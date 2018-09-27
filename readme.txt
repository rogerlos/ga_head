=== GA Head ===
Contributors: rogerlos
Donate link:
Tags: head, googleanalytics, analytics
Requires at least: 4.5
Tested up to: 4.9.8
Requires PHP: 5.4
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

GA Head adds Google Analytics to your site's head, as recommended by Google. Configured via customizer.

== Description ==

GA Head adds Google Analytics code to your site's html head, as recommended by Google.

All you have to do is add your Analytics tracking code via the Customizer, and you're set!

Additional options:

* Add a WordPress capability to keep users who have that capability from being tracked...handy if you don't want
Site Administrator's use of the site added to your google data, for example.
* Use your own custom JavaScript instead of the standard Google "isogram" code.

If you are a developer, please see the plugin's [Github repository](https://github.com/rogerlos/ga_head)
for additional documentation. GA Head provides filters for nearly every internal method it uses, allowing much more
sophisticated tracking to meet your requirements. There is also a helper function for use when your templates do not
call `wp_head()`;

== Installation ==

1. Upload the directory `ga-head` to your `plugins` directory, or select `ga-head.zip` by using the "Add New" button
on the WordPress admin "Plugins" page.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the tracking code you acquired from Google by opening the Customizer and finding the "Google Analytics" section

== Changelog ==

= 1.3.1 =
* First version in public repository

= 1.0 =
* Initial release

== Support ==

Issues opened in the [github issue tracker](https://github.com/rogerlos/ga_head) will get a speedier response.
Note: I do not provide general Google Analytics support!