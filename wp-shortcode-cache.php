<?php
/*
Plugin Name: WP Shortcode Cache
Plugin URI:  https://wordpress.org/plugins/wp-shortcode-cache/
Description: Adds a customizable cache layer to all shortcodes in WordPress.
Version:     1.0.0
Author:      Felix Arntz
Author URI:  https://leaves-and-love.net
License:     GNU General Public License v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: wp-shortcode-cache
Tags:        shortcode, cache, performance
*/
/**
 * Plugin initialization file
 *
 * @package WPShortcodeCache
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Initializes the plugin.
 *
 * @since 1.0.0
 */
function wp_shortcode_cache_init() {
	if ( class_exists( 'WP_Shortcode_Cache' ) ) {
		return;
	}

	$plugin_path = plugin_dir_path( __FILE__ );

	require_once $plugin_path . 'wp-shortcode-cache/class-wp-shortcode-cache-tag.php';
	require_once $plugin_path . 'wp-shortcode-cache/class-wp-shortcode-cache.php';
	require_once $plugin_path . 'wp-shortcode-cache/functions.php';

	$shortcode_cache = wp_shortcode_cache();
	add_filter( 'pre_do_shortcode_tag', array( $shortcode_cache, 'maybe_return_cached_output' ), 100, 4 );
	add_filter( 'do_shortcode_tag',     array( $shortcode_cache, 'maybe_cache_output' ),         1,   4 );
}

/**
 * Shows an admin notice if the WordPress version installed is not supported.
 *
 * @since 1.0.0
 */
function wp_shortcode_cache_wordpress_version_notice() {
	$plugin_file = plugin_basename( __FILE__ );
	?>
	<div class="notice notice-warning is-dismissible">
		<p>
			<?php printf(
				__( 'Please note: WP Shortcode Cache requires WordPress 4.7-beta or higher. <a href="%s">Deactivate plugin</a>.', 'wp-shortcode-cache' ),
				wp_nonce_url(
					add_query_arg(
						array(
							'action'        => 'deactivate',
							'plugin'        => $plugin_file,
							'plugin_status' => 'all',
						),
						self_admin_url( 'plugins.php' )
					),
					'deactivate-plugin_' . $plugin_file
				)
			); ?>
		</p>
	</div>
	<?php
}

if ( version_compare( $GLOBALS['wp_version'], '4.7-beta', '<' ) ) {
	add_action( 'admin_notices', 'wp_shortcode_cache_wordpress_version_notice' );
} else {
	add_action( 'plugins_loaded', 'wp_shortcode_cache_init' );
}
