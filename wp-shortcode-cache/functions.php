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

/**
 * Registers multiple external data values for a given shortcode.
 *
 * @since 1.0.0
 *
 * @see WP_Shortcode_Cache::register_external_data_values()
 *
 * @param string $tag           Shortcode name.
 * @param array  $external_data Array of $identifier => $params pairs. Each $params
 *                              element can either be a string used as `name`, or for
 *                              more complex use-cases an array containing a `name` key,
 *                              and optionally a `type` key.
 * @return bool|WP_Error True on success, error object on failure.
 */
function wp_shortcode_cache_register_external_data_values( $tag, $external_data ) {
	return wp_shortcode_cache()->register_external_data_values( $tag, $external_data );
}

/**
 * Registers an external data value for a given shortcode.
 *
 * @since 1.0.0
 *
 * @see WP_Shortcode_Cache::register_external_data_value()
 *
 * @param string          $tag             Shortcode name.
 * @param string          $data_identifier Unique identifier for this external data value. This value is
 *                                         used as array key for external data. The name of an existing
 *                                         shortcode attribute may be passed so that this value acts as
 *                                         a fallback.
 * @param string|callable $data_name       Name of global key, or callback function if $type is 'callback'.
 * @param string          $data_type       Optional. Either 'callback', 'global', 'request', 'get', 'post'
 *                                         or 'session'. Default 'global'.
 * @return bool|WP_Error True on success, error object on failure.
 */
function wp_shortcode_cache_register_external_data_value( $tag, $data_identifier, $data_name, $data_type = 'global' ) {
	return wp_shortcode_cache()->register_external_data_value( $tag, $data_identifier, $data_name, $data_type );
}

/**
 * Unregisters an external data value for a given shortcode.
 *
 * @since 1.0.0
 *
 * @see WP_Shortcode_Cache::unregister_external_data_value()
 *
 * @param string $tag             Shortcode name.
 * @param string $data_identifier Unique identifier of the external data value to unregister.
 * @return bool|WP_Error True on success, error object on failure.
 */
function wp_shortcode_cache_unregister_external_data_value( $tag, $data_identifier ) {
	return wp_shortcode_cache()->unregister_external_data_value( $tag, $data_identifier );
}

/**
 * Sets the duration for which a given shortcode should be cached.
 *
 * @since 1.0.0
 *
 * @see WP_Shortcode_Cache::set_cache_duration()
 *
 * @param string $tag      Shortcode name.
 * @param int    $duration Cache duration in seconds. Set to 0 for no expiration.
 * @return bool|WP_Error True on success, error object on failure.
 */
function wp_shortcode_cache_set_cache_duration( $tag, $duration ) {
	return wp_shortcode_cache()->set_cache_duration( $tag, $duration );
}
