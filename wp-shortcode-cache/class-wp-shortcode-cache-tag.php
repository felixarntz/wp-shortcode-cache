<?php
/**
 * WP_Shortcode_Cache_Tag class
 *
 * @package WPShortcodeCache
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing a specific shortcode and its external cache data.
 *
 * @since 1.0.0
 */
class WP_Shortcode_Cache_Tag {
	/**
	 * Shortcode name.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $tag;

	/**
	 * Registered callbacks for external data.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $callbacks = array();

	/**
	 * Registered globals and superglobals for external data.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $globals = array();

	/**
	 * Duration for which to cache this shortcode.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var int
	 */
	private $duration = 0;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see WP_Shortcode_Cache_Tag::register_external_data_values()
	 *
	 * @param string     $tag           Shortcode name.
	 * @param array|null $external_data Optional. External data values to register. Default null.
	 */
	public function __construct( $tag, $external_data = null ) {
		$this->tag = $tag;

		if ( is_array( $external_data ) ) {
			$this->register_external_data_values( $external_data );
		}
	}

	/**
	 * Registers multiple external data values.
	 *
	 * This method is a shortcut for calling WP_Shortcode_Cache_Tag::register_external_data_value()
	 * multiple times.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see WP_Shortcode_Cache_Tag::register_external_data_value()
	 *
	 * @param array $external_data Array of $identifier => $params pairs. Each $params
	 *                             element can either be a string used as `name`, or for
	 *                             more complex use-cases an array containing a `name` key,
	 *                             and optionally a `type` key.
	 * @return bool|WP_Error True on success, error object on failure.
	 */
	public function register_external_data_values( $external_data ) {
		$error = new WP_Error();

		foreach ( $external_data as $identifier => $params ) {
			if ( is_string( $params ) ) {
				$result = register_external_data_value( $identifier, $params );
				if ( is_wp_error( $result ) ) {
					$error->add( $result->get_error_code(), $result->get_error_message() );
				}
			} elseif ( ! isset( $params['name'] ) ) {
				/* translators: %s: shortcode name */
				$error->add( 'missing_external_data_value_name', __( 'The name argument is missing for external cache data registered for shortcode %s.', 'wp-shortcode-cache' ), esc_attr( $this->tag ) );
			} else {
				$result = register_external_data_value( $identifier, $params['name'], isset( $params['type'] ) ? $params['type'] : 'global' );
				if ( is_wp_error( $result ) ) {
					$error->add( $result->get_error_code(), $result->get_error_message() );
				}
			}
		}

		if ( ! empty( $error->errors ) ) {
			return $error;
		}

		return true;
	}

	/**
	 * Registers an external data value.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string          $identifier Unique identifier for this external data value. This value is
	 *                                    used as array key for external data. The name of an existing
	 *                                    shortcode attribute may be passed so that this value acts as
	 *                                    a fallback.
	 * @param string|callable $name       Name of global key, or callback function if $type is 'callback'.
	 * @param string          $type       Optional. Either 'callback', 'global', 'request', 'get', 'post'
	 *                                    or 'session'. Default 'global'.
	 * @return bool|WP_Error True on success, error object on failure.
	 */
	public function register_external_data_value( $identifier, $name, $type = 'global' ) {
		switch ( $type ) {
			case 'callback':
				$this->callbacks[ $identifier ] = array(
					'name'         => $name,
				);
				return true;
			case 'global':
			case 'request':
			case 'get':
			case 'post':
			case 'session':
				$this->globals[ $identifier ] = array(
					'name'         => $name,
					'type'         => $type,
				);
				return true;
		}

		/* translators: 1: type, 2: shortcode name */
		return new WP_Error( 'invalid_external_data_value_type', sprintf( __( '%s is not a valid type for external cache data registered for shortcode %2$s.', 'wp-shortcode-cache' ), esc_attr( $type ), esc_attr( $this->tag ) ) );
	}

	/**
	 * Unregisters an external data value.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $identifier Unique identifier of the external data value to unregister.
	 * @return bool|WP_Error True on success, error object on failure.
	 */
	public function unregister_external_data_value( $identifier ) {
		if ( isset( $this->callbacks[ $identifier ] ) ) {
			unset( $this->callbacks[ $identifier ] );
			return true;
		}

		if ( isset( $this->globals[ $identifier ] ) ) {
			unset( $this->globals[ $identifier ] );
			return true;
		}

		return new WP_Error( 'invalid_external_data_value_identifier', sprintf( __( 'No external cache data value with identifier %1$s is registered for shortcode %2$s.', 'wp-shortcode-tag' ), esc_attr( $identifier ), esc_attr( $this->tag ) ) );
	}

	/**
	 * Sets the duration for which this shortcode should be cached.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $duration Cache duration in seconds. Set to 0 for no expiration.
	 * @return bool|WP_Error True on success, error object on failure.
	 */
	public function set_cache_duration( $duration ) {
		$this->duration = absint( $duration );

		return true;
	}

	/**
	 * Returns the duration for which this shortcode should be cached.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return int Cache duration in seconds, or 0 for no expiration.
	 */
	public function get_cache_duration() {
		return $this->duration;
	}

	/**
	 * Fills the regular shortcode attributes array with external data.
	 *
	 * The return value of this method is safe to be used as a set of data for
	 * generating a unique cache key.
	 *
	 * The external data is fetched using the registered external data values.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $attr Shortcode attributes.
	 * @return array Shortcode attributes including external data.
	 */
	public function fill_external_data( $attr ) {
		foreach ( $this->callbacks as $identifier => $params ) {
			if ( isset( $attr[ $identifier ] ) ) {
				continue;
			}

			/* Special case for get_post() function. */
			if ( 'get_post' === $params['name'] ) {
				$attr = $this->fill_current_post( $attr, $identifier );
			} else {
				if ( is_callable( $params['name'] ) ) {
					$attr[ $identifier ] = call_user_func( $params['name'] );
				}
			}
		}

		foreach ( $this->globals as $identifier => $params ) {
			if ( isset( $attr[ $identifier ] ) ) {
				continue;
			}

			/* Special case for global $post. */
			if ( 'global' === $params['type'] && 'post' === $params['name'] ) {
				$attr = $this->fill_current_post( $attr, $identifier );
			} else {
				switch ( $params['type'] ) {
					case 'global':
						if ( isset( $GLOBALS[ $params['name'] ] ) ) {
							$attr[ $identifier ] = $GLOBALS[ $params['name'] ];
						}
						break;
					case 'request':
						if ( isset( $_REQUEST[ $params['name'] ] ) ) {
							$attr[ $identifier ] = $_REQUEST[ $params['name'] ];
						}
						break;
					case 'get':
						if ( isset( $_GET[ $params['name'] ] ) ) {
							$attr[ $identifier ] = $_GET[ $params['name'] ];
						}
						break;
					case 'post':
						if ( isset( $_POST[ $params['name'] ] ) ) {
							$attr[ $identifier ] = $_POST[ $params['name'] ];
						}
						break;
					case 'session':
						if ( isset( $_SESSION[ $params['name'] ] ) ) {
							$attr[ $identifier ] = $_SESSION[ $params['name'] ];
						}
						break;
				}
			}
		}

		return $attr;
	}

	/**
	 * Fills the regular shortcode attributes array with the current post.
	 *
	 * This is a utility method to handle latest changes in that post automatically.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array  $attr       Shortcode attributes.
	 * @param string $identifier Unique identifier to the field the post should be stored in.
	 * @return array Shortcode attributes including the current post data.
	 */
	private function fill_current_post( $attr, $identifier ) {
		$post = get_post();

		if ( $post ) {
			$attr[ $identifier ] = $post->ID;
			$attr[ $identifier . '_last_changed' ] = $post->post_modified_gmt;
		}

		return $attr;
	}
}
