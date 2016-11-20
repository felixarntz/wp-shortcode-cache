<?php
/**
 * Utility functions
 *
 * @package WPShortcodeCache
 * @since 1.0.0
 */

/**
 * Returns the main instance of the WP_Shortcode_Cache class.
 *
 * @since 1.0.0
 *
 * @return WP_Shortcode_Cache The main instance.
 */
function wp_shortcode_cache() {
	return WP_Shortcode_Cache::instance();
}
