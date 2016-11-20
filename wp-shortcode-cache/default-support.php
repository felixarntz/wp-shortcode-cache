<?php
/**
 * Support for Core and several popular plugin shortcodes.
 *
 * @package WPShortcodeCache
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers support for WordPress and several popular plugins.
 *
 * More plugins will be added over time.
 *
 * @since 1.0.0
 */
function wp_shortcode_cache_register_support() {
	wp_shortcode_cache_register_core_support();

	if ( class_exists( 'Easy_Digital_Downloads' ) ) {
		wp_shortcode_cache_register_edd_support();
	}
}

/**
 * Registers support for WordPress Core shortcodes.
 *
 * @since 1.0.0
 */
function wp_shortcode_cache_register_core_support() {
	wp_shortcode_cache_register_external_data_values( 'caption', array() );
	wp_shortcode_cache_set_cache_duration( 'caption', 0 );

	wp_shortcode_cache_register_external_data_values( 'wp_caption', array() );
	wp_shortcode_cache_set_cache_duration( 'wp_caption', 0 );

	/* Prevent caching for these since there is no way to handle the instance count. */
	add_filter( 'wp_shortcode_cache_use_cache_gallery',  '__return_false' );
	add_filter( 'wp_shortcode_cache_use_cache_playlist', '__return_false' );
	add_filter( 'wp_shortcode_cache_use_cache_audio',    '__return_false' );
	add_filter( 'wp_shortcode_cache_use_cache_video',    '__return_false' );
}

/**
 * Registers support for Easy Digital Downloads shortcodes.
 *
 * @since 1.0.0
 */
function wp_shortcode_cache_register_edd_support() {
	wp_shortcode_cache_register_external_data_values( 'purchase_link', array(
		'id'                 => array(
			'name' => 'post',
			'type' => 'global',
		),
		'displayed_form_ids' => array(
			'name' => 'edd_displayed_form_ids',
			'type' => 'global',
		),
		'style'              => array(
			'name' => 'edd_get_option',
			'type' => 'callback',
			'args' => array( 'button_style', 'button' ),
		),
		'color'              => array(
			'name' => 'edd_get_option',
			'type' => 'callback',
			'args' => array( 'checkout_color', 'blue' ),
		),
	) );
	wp_shortcode_cache_set_cache_duration( 'purchase_link', 0 );

	wp_shortcode_cache_register_external_data_values( 'purchase_collection', array(
		'style' => array(
			'name' => 'edd_get_option',
			'type' => 'callback',
			'args' => array( 'button_style', 'button' ),
		),
		'color' => array(
			'name' => 'edd_get_option',
			'type' => 'callback',
			'args' => array( 'checkout_color', 'blue' ),
		),
	) );
	wp_shortcode_cache_set_cache_duration( 'purchase_collection', HOUR_IN_SECONDS );

	foreach ( array( 'download_history', 'purchase_history' ) as $tag ) {
		wp_shortcode_cache_register_external_data_values( $tag, array(
			'user_id'            => array(
				'name' => 'get_current_user_id',
				'type' => 'callback',
			),
			'pending'            => array(
				'name' => 'edd_user_pending_verification',
				'type' => 'callback',
			),
			'edd_verify_success' => array(
				'name' => 'edd-verify-success',
				'type' => 'get',
			),
		) );
		wp_shortcode_cache_set_cache_duration( $tag, HOUR_IN_SECONDS );
	}

	wp_shortcode_cache_register_external_data_values( 'edd_login', array(
		'redirect' => array(
			'name' => 'edd_get_option',
			'type' => 'callback',
			'args' => array( 'login_redirect_page', '' ),
		),
	) );
	wp_shortcode_cache_set_cache_duration( 'edd_login', 0 );

	wp_shortcode_cache_register_external_data_values( 'edd_register', array(
		'redirect' => array(
			'name' => 'edd_get_option',
			'type' => 'callback',
			'args' => array( 'purchase_history_page', '' ),
		),
	) );
	wp_shortcode_cache_set_cache_duration( 'edd_register', 0 );

	wp_shortcode_cache_register_external_data_values( 'downloads', array(
		'paged' => array(
			'name' => 'get_query_var',
			'type' => 'callback',
			'args' => array( 'paged' ),
		),
	) );
	wp_shortcode_cache_set_cache_duration( 'downloads', HOUR_IN_SECONDS );

	wp_shortcode_cache_register_external_data_values( 'edd_price', array(
		'id' => array(
			'name' => 'get_post',
			'type' => 'callback',
		),
	) );
	wp_shortcode_cache_set_cache_duration( 'edd_price', 0 );

	wp_shortcode_cache_register_external_data_values( 'edd_profile_editor', array(
		'user_id' => array(
			'name' => 'get_current_user_id',
			'type' => 'callback',
		),
		'pending' => array(
			'name' => 'edd_user_pending_verification',
			'type' => 'callback',
		),
		'updated' => array(
			'name' => 'updated',
			'type' => 'get',
		),
	) );
	wp_shortcode_cache_set_cache_duration( 'edd_profile_editor', HOUR_IN_SECONDS );

	/* Prevent caching for these since the logic is way too complex. */
	add_filter( 'wp_shortcode_cache_use_cache_download_checkout',  '__return_false' );
	add_filter( 'wp_shortcode_cache_use_cache_download_cart',      '__return_false' );
	add_filter( 'wp_shortcode_cache_use_cache_download_discounts', '__return_false' );
	add_filter( 'wp_shortcode_cache_use_cache_edd_receipt',        '__return_false' );
}
