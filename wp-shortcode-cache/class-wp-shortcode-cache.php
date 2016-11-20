<?php
/**
 * WP_Shortcode_Cache class
 *
 * @package WPShortcodeCache
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class used for managing shortcode cache.
 *
 * @since 1.0.0
 */
class WP_Shortcode_Cache {
	/**
	 * Cache group for shortcodes.
	 */
	const CACHE_GROUP = 'shortcodes';

	/**
	 * Registered shortcode cache tags.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $tags = array();

	/**
	 * The main instance of the class.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @var WP_Shortcode_Cache|null
	 */
	private static $instance = null;

	/**
	 * Registers multiple external data values for a given shortcode.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see WP_Shortcode_Cache_Tag::register_external_data_values()
	 *
	 * @param string $tag           Shortcode name.
	 * @param array  $external_data Array of $identifier => $params pairs. Each $params
	 *                              element can either be a string used as `name`, or for
	 *                              more complex use-cases an array containing a `name` key,
	 *                              and optionally a `type` key.
	 * @return bool|WP_Error True on success, error object on failure.
	 */
	public function register_external_data_values( $tag, $external_data ) {
		if ( ! isset( $this->tags[ $tag ] ) ) {
			$this->tags[ $tag ] = new WP_Shortcode_Cache_Tag( $tag );
		}

		return $this->tags[ $tag ]->register_external_data_values( $external_data );
	}

	/**
	 * Registers an external data value for a given shortcode.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see WP_Shortcode_Cache_Tag::register_external_data_value()
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
	public function register_external_data_value( $tag, $data_identifier, $data_name, $data_type = 'global' ) {
		if ( ! isset( $this->tags[ $tag ] ) ) {
			$this->tags[ $tag ] = new WP_Shortcode_Cache_Tag( $tag );
		}

		return $this->tags[ $tag ]->register_external_data_value( $data_identifier, $data_name, $data_type );
	}

	/**
	 * Unregisters an external data value for a given shortcode.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see WP_Shortcode_Cache_Tag::unregister_external_data_value()
	 *
	 * @param string $tag             Shortcode name.
	 * @param string $data_identifier Unique identifier of the external data value to unregister.
	 * @return bool|WP_Error True on success, error object on failure.
	 */
	public function unregister_external_data_value( $tag, $data_identifier ) {
		if ( ! isset( $this->tags[ $tag ] ) ) {
			/* translators: %s: shortcode name */
			return new WP_Error( 'no_external_data', sprintf( __( 'No external cache data is registered for shortcode %s.', 'wp-shortcode-tag' ), esc_attr( $tag ) ) );
		}

		return $this->tags[ $tag ]->unregister_external_data_value( $data_identifier );
	}

	/**
	 * Sets the duration for which a given shortcode should be cached.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see WP_Shortcode_Cache_Tag::set_cache_duration()
	 *
	 * @param string $tag      Shortcode name.
	 * @param int    $duration Cache duration in seconds. Set to 0 for no expiration.
	 * @return bool|WP_Error True on success, error object on failure.
	 */
	public function set_cache_duration( $tag, $duration ) {
		if ( ! isset( $this->tags[ $tag ] ) ) {
			$this->tags[ $tag ] = new WP_Shortcode_Cache_Tag( $tag );
		}

		return $this->tags[ $tag ]->set_cache_duration( $duration );
	}

	/**
	 * Tries to fetch cached output for the passed shortcode and its data.
	 *
	 * If caching has been disabled for this shortcode via the `use_cache()`
	 * method, the method won't do anything.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool|string $output  Return value of the short-circuit. Will be set to
	 *                             false unless another filter has already modified it.
	 * @param string      $tag     Shortcode name.
	 * @param array       $attr    Shortcode attributes array.
	 * @param array       $matches Regular expression match array.
	 * @return bool|string The cached output if found, or the original input value.
	 */
	public function maybe_return_cached_output( $output, $tag, $attr, $matches ) {
		if ( ! $this->use_cache( $tag, $output, true ) ) {
			return $output;
		}

		$cache_data = $this->retrieve_cache_data( $tag, $attr, $matches );
		$cache_key = $this->get_cache_key( $tag, $cache_data );

		$cached_output = $this->get_cached_output( $cache_key );
		if ( false === $cached_output ) {
			return $output;
		}

		return $cached_output;
	}

	/**
	 * Caches the output for the passed shortcode and its data.
	 *
	 * If caching has been disabled for this shortcode via the `use_cache()`
	 * method, the method won't do anything.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $output  Shortcode output.
	 * @param string $tag     Shortcode name.
	 * @param array  $attr    Shortcode attributes array.
	 * @param array  $matches Regular expression match array.
	 * @return string Original shortcode output, passed through.
	 */
	public function maybe_cache_output( $output, $tag, $attr, $matches ) {
		if ( ! $this->use_cache( $tag, $output, false ) ) {
			return $output;
		}

		$cache_data = $this->retrieve_cache_data( $tag, $attr, $matches );
		$cache_key = $this->get_cache_key( $tag, $cache_data );

		$cache_duration = $this->get_cache_duration( $tag );

		$this->set_cached_output( $cache_key, $output, $cache_duration );

		return $output;
	}

	/**
	 * Gets a cached value.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $cache_key Cache key.
	 * @return mixed Cached value, or false if nothing found.
	 */
	public function get_cached_output( $cache_key ) {
		return wp_cache_get( $cache_key, self::CACHE_GROUP );
	}

	/**
	 * Caches a value.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $cache_key Cache key.
	 * @param mixed  $value     Value to cache under that key.
	 * @param int    $expire    Optional. When the cache data should expire, in seconds.
	 *                          Default 0 (no expiration).
	 */
	public function set_cached_output( $cache_key, $value, $expire = 0 ) {
		wp_cache_set( $cache_key, $value, self::CACHE_GROUP );
	}

	/**
	 * Generates a unique cache key that identifies the given shortcode and its data.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $tag  Shortcode name.
	 * @param array  $data Array of shortcode data.
	 * @return string Unique cache key.
	 */
	private function get_cache_key( $tag, $data ) {
		$key = md5( serialize( $data ) );

		return "$tag:$key";
	}

	/**
	 * Retrieves all data relevant to create a unique cache key for a given shortcode.
	 *
	 * The data array generated by this method is ready to be passed on to
	 * `WP_Shortcode_Cache::get_cache_key()`.
	 *
	 * This method also takes external data, like the values of global variables or
	 * results of callbacks, into account if that data has been specifically registered
	 * for the given shortcode.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @see WP_Shortcode_Cache::get_cache_key()
	 *
	 * @param string $tag     Shortcode name.
	 * @param array  $attr    Shortcode attributes array.
	 * @param array  $matches Regular expression match array.
	 * @return array Array of all relevant shortcode data.
	 */
	private function retrieve_cache_data( $tag, $attr, $matches ) {
		if ( isset( $this->tags[ $tag ] ) ) {
			$attr = $this->tags[ $tag ]->fill_external_data( $attr );
		} else {
			/* By default, always include global $post and the current user. */
			$post = get_post();
			if ( $post ) {
				$attr['__post_id'] = $post->ID;
				$attr['__post_last_changed'] = $post->post_modified_gmt;
			}
			$user_id = get_current_user_id();
			if ( $user_id > 0 ) {
				$attr['__user_id'] = $user_id;
			}
		}

		$attr['__content'] = isset( $matches[5] ) ? $matches[5] : null;

		return $attr;
	}

	/**
	 * Retrieves the cache duration a given shortcode should be cached.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $tag Shortcode name.
	 * @return int Cache duration in seconds, or 0 for no expiration.
	 */
	private function get_cache_duration( $tag ) {
		if ( isset( $this->tags[ $tag ] ) ) {
			return $this->tags[ $tag ]->get_cache_duration();
		}

		/* By default, always cache for an hour for the current user. */
		$user_id = get_current_user_id();
		if ( $user_id > 0 ) {
			return HOUR_IN_SECONDS;
		}

		return 0;
	}

	/**
	 * Checks whether the cache should be used for a given shortcode.
	 *
	 * By default, all shortcodes use the cache unless their pre-filter
	 * has already been used by another hook callback.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string      $tag    Shortcode name.
	 * @param bool|string $output Optional. Generated shortcode output, or false.
	 *                            Default false.
	 * @param bool        $pre    Optional. Whether this check is being run prior
	 *                            to shortcode execution. Default false.
	 * @return bool True if the cache should be used, false otherwise.
	 */
	private function use_cache( $tag, $output = false, $pre = false ) {
		$use_cache = ! $pre || false === $output;

		/**
		 * Filters whether the cache should be used for the given shortcode.
		 *
		 * The dynamic portion of the hook name, `$tag`, refers to the shortcode name.
		 *
		 * @since 1.0.0
		 *
		 * @param bool        $use_cache Whether the cache should be used.
		 * @param bool|string $output    Generated shortcode output, or false if being run
		 *                               prior to shortcode execution unless another filter
		 *                               has already modified it.
		 * @param bool        $pre       Whether the check is being made before
		 *                               generating the shortcode output.
		 */
		return apply_filters( "wp_shortcode_cache_use_cache_{$tag}", $use_cache, $output, $pre );
	}

	/**
	 * Singleton.
	 *
	 * This method should be used to retrieve the main instance of the class.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return WP_Shortcode_Cache The main instance.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
