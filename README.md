[![WordPress plugin](https://img.shields.io/wordpress/plugin/v/wp-shortcode-cache.svg?maxAge=2592000)](https://wordpress.org/plugins/wp-shortcode-cache/)
[![WordPress](https://img.shields.io/wordpress/v/wp-shortcode-cache.svg?maxAge=2592000)](https://wordpress.org/plugins/wp-shortcode-cache/)
[![Code Climate](https://codeclimate.com/github/felixarntz/wp-shortcode-cache/badges/gpa.svg)](https://codeclimate.com/github/felixarntz/wp-shortcode-cache)
[![Latest Stable Version](https://poser.pugx.org/felixarntz/wp-shortcode-cache/version)](https://packagist.org/packages/felixarntz/wp-shortcode-cache)
[![License](https://poser.pugx.org/felixarntz/wp-shortcode-cache/license)](https://packagist.org/packages/felixarntz/wp-shortcode-cache)

# WP Shortcode Cache

Adds a customizable cache layer to all shortcodes in WordPress.

## Usage

After having installed and activated the plugin, all shortcodes will use the plugin's caching mechanism for a performance benefit. To take full advantage of the feature, your site should use a persistent object cache like Redis or Memcache.

The plugin will work properly out of the box for all shortcodes that entirely rely on data passed through shortcode attributes or the shortcode's content. If a shortcode uses external data, for example from globals, this data must be registered, otherwise that data is not considered when creating the unique cache key which can result in incorrect cache return values. By default the plugin automatically tries to detect whether a shortcode uses the `$post` global - however the method used is not a 100 percent reliable, so it is always encouraged to register all external data that is used from within the shortcode (each set of data can either be the result of a callback function or the value of a global variable).

## Requirements

* WordPress >= 4.7-beta
