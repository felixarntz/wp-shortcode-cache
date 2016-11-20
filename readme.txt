=== WP Shortcode Cache ===

Plugin Name:       WP Shortcode Cache
Plugin URI:        https://wordpress.org/plugins/wp-shortcode-cache/
Author:            Felix Arntz
Author URI:        https://leaves-and-love.net
Contributors:      flixos90
Donate link:       https://leaves-and-love.net/wordpress-plugins/
Requires at least: 4.7-beta
Tested up to:      4.7
Stable tag:        1.0.0
Version:           1.0.0
License:           GNU General Public License v3
License URI:       http://www.gnu.org/licenses/gpl-3.0.html
Tags:              shortcode, cache, performance

Adds a customizable cache layer to all shortcodes in WordPress.

== Description ==

After having installed and activated the plugin, all shortcodes will use the plugin's caching mechanism for a performance benefit. To take full advantage of the feature, your site should use a persistent object cache like Redis or Memcache.

The plugin will work properly out of the box for all shortcodes that entirely rely on data passed through shortcode attributes or the shortcode's content. If a shortcode uses external data, for example from globals, this data must be registered, otherwise that data is not considered when creating the unique cache key which can result in incorrect cache return values. By default the plugin automatically tries to detect whether a shortcode uses the `$post` global - however the method used is not a 100 percent reliable, so it is always encouraged to register all external data that is used from within the shortcode (each set of data can either be the result of a callback function or the value of a global variable).

= Find the plugin here =

* [GitHub](https://github.com/felixarntz/wp-shortcode-cache)
* [Translations](https://translate.wordpress.org/projects/wp-plugins/wp-shortcode-cache)

== Installation ==

1. Upload the entire `wp-shortcode-cache` folder to the `/wp-content/plugins/` directory or download it through the WordPress backend.
2. Activate the plugin through the 'Plugins' menu in WordPress.

= Where should I submit my support request? =

I preferably take support requests as [issues on GitHub](https://github.com/felixarntz/wp-shortcode-cache/issues), so I would appreciate if you created an issue for your request there. However, if you don't have an account there and do not want to sign up, you can of course use the [wordpress.org support forums](https://wordpress.org/support/plugin/wp-shortcode-cache) as well.

= How can I contribute to the plugin? =

If you're a developer and you have some ideas to improve the plugin or to solve a bug, feel free to raise an issue or submit a pull request in the [GitHub repository for the plugin](https://github.com/felixarntz/wp-shortcode-cache).

You can also contribute to the plugin by translating it. Simply visit [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/wp-shortcode-cache) to get started.

== Changelog ==

= 1.0.0 =
* First stable version
