<?php
/**
 * Template functions.
 *
 * @package Classy
 */

/**
 * Grab the Classy object and return it.
 * Wrapper for Classy::get_instance()
 *
 * @since  0.1.0
 * @return \Classy\Classy  Singleton instance of plugin class.
 */
function get_theme_framework() {
	return \Classy\Classy::get_instance();
}
