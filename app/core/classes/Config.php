<?php
/**
 * Theme Config.
 *
 * Loads theme config and registers models based on it.
 *
 * @package Classy
 */

namespace Classy;

/**
 * Class Config.
 */
class Config {

	/**
	 * Contains all vars from config file.
	 *
	 * @var null
	 */
	protected static $vars = null;

	/**
	 * Returns list of allowed variables that can be used in theme config.
	 *
	 * @return array
	 */
	private static function get_allowed_variables() {
		return array(
			'environment',
			'textdomain',
			'minify_html',
			'post_types',
			'taxonomies',
			'post_formats',
			'sidebars',
			'include',
		);
	}

	/**
	 * Requires config file variables
	 *
	 * @return array
	 */
	public static function get_vars() {
		if ( is_null( self::$vars ) ) {
			// Check for a theme config.
			$config_file = CLASSY_THEME_FRAMEWORK_PATH . '/custom/config.php';

			if ( ! file_exists( $config_file ) ) {
				wp_die( sprintf(
					'There is no config file in %s custom/config.php',
					esc_html( CLASSY_THEME )
				) );
			}

			require_once( $config_file );
			$vars = self::get_allowed_variables();

			foreach ( $vars as $var ) {
				if ( isset( $$var ) ) {
					self::$vars[ $var ] = $$var;

					unset( $$var ); // We don't require it anymore.
				}
			}
		}

		return self::$vars;
	}

	/**
	 * Retrieves config variables and then init WordPress functionality based on them.
	 */
	public static function init() {
		$vars = self::get_vars();

		// Init Post Types.
		if ( isset( $vars['post_types'] ) ) {
			self::init_post_types( $vars['post_types'] );
		}

		// Init Taxonomies.
		if ( isset( $vars['taxonomies'] ) ) {
			self::init_taxonomies( $vars['taxonomies'] );
		}

		// Init Post Formats.
		if ( isset( $vars['post_formats'] ) ) {
			self::init_post_formats( $vars['post_formats'] );
		}

		// Init Sidebars.
		if ( isset( $vars['sidebars'] ) ) {
			self::init_sidebars( $vars['sidebars'] );
		}
	}

	/**
	 * Registers Post Types.
	 *
	 * @param array $post_types Custom post types to be registered.
	 */
	private static function init_post_types( $post_types ) {
		if ( is_array( $post_types ) ) {
			foreach ( $post_types as $type => $options ) {
				self::add_post_type(
					$type,
					$options['config'],
					$options['singular'],
					$options['multiple']
				);
			}
		}
	}

	/**
	 * Wrapper for register_post_type().
	 *
	 * @param string $name     Post type key, must not exceed 20 characters.
	 * @param array  $config   Better look into register_post_type() function.
	 * @param string $singular Optional. Default singular name.
	 * @param string $multiple Optional. Default multiple name.
	 */
	private static function add_post_type( $name, $config, $singular = 'Entry', $multiple = 'Entries' ) {
		$domain = Classy::textdomain();

		if ( ! isset( $config['labels'] ) ) {
			$config['labels'] = array(
				'name' => __( $multiple, $domain ),
				'singular_name' => __( $singular, $domain ),
				'not_found' => __( 'No ' . $multiple . ' Found', $domain ),
				'not_found_in_trash' => __( 'No ' . $multiple . ' found in Trash', $domain ),
				'edit_item' => __( 'Edit ', $singular, $domain ),
				'search_items' => __( 'Search ' . $multiple, $domain ),
				'view_item' => __( 'View ', $singular, $domain ),
				'new_item' => __( 'New ' . $singular, $domain ),
				'add_new' => __( 'Add New', $domain ),
				'add_new_item' => __( 'Add New ' . $singular, $domain ),
			);
		}

		register_post_type( $name, $config );
	}

	/**
	 * Registers taxonomies.
	 *
	 * @param array $taxonomies Taxonomies to be registered.
	 */
	private static function init_taxonomies( $taxonomies ) {
		if ( is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $type => $options ) {
				self::add_taxonomy(
					$type,
					$options['for'],
					$options['config'],
					$options['singular'],
					$options['multiple']
				);
			}
		}
	}

	/**
	 * Wrapper for register_taxonomy().
	 *
	 * @param string $name 		  Taxonomy key, must not exceed 32 characters.
	 * @param mixed  $object_type Name of the object type for the taxonomy object.
	 * @param array  $config	  Better look into register_taxonomy() function.
	 * @param string $singular    Optional. Default singular name.
	 * @param string $multiple 	  Optional. Default multiple name.
	 */
	private static function add_taxonomy( $name, $object_type, $config, $singular = 'Entry', $multiple = 'Entries' ) {
		$domain = Classy::textdomain();

		if ( ! isset( $config['labels'] ) ) {
			$config['labels'] = array(
				'name' => __( $multiple, $domain ),
				'singular_name' => __( $singular, $domain ),
				'search_items' => __( 'Search ' . $multiple, $domain ),
				'all_items' => __( 'All ' . $multiple, $domain ),
				'parent_item' => __( 'Parent ' . $singular, $domain ),
				'parent_item_colon' => __( 'Parent ' . $singular . ':', $domain ),
				'edit_item' => __( 'Edit ' . $singular, $domain ),
				'update_item' => __( 'Update ' . $singular, $domain ),
				'add_new_item' => __( 'Add New ' . $singular, $domain ),
				'new_item_name' => __( 'New ' . $singular . ' Name', $domain ),
				'menu_name' => __( $singular, $domain ),
			);
		}

		register_taxonomy( $name, $object_type, $config );
	}

	/**
	 * Registers Post Formats.
	 *
	 * @param array $post_formats Array with available post formats.
	 */
	private static function init_post_formats( $post_formats ) {
		if ( is_array( $post_formats ) ) {
			add_theme_support( 'post-formats', $post_formats );
		}
	}

	/**
	 * Wrapper for register_sidebar().
	 *
	 * @param array $sidebars Sidebars to be registered.
	 */
	private static function init_sidebars( $sidebars ) {
		$domain = Classy::textdomain();

		if ( is_array( $sidebars ) ) {
			foreach ( $sidebars as $id => $title ) {
				register_sidebar(
					array(
						'id' => $id,
						'name' => __( $title, $domain ),
						'description' => __( $title, $domain ),
						'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-inner">',
						'after_widget' => '</div></div>',
						'before_title' => '<h3>',
						'after_title' => '</h3>',
					)
				);
			}
		}
	}
}
