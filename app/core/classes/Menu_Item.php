<?php
/**
 * Class for handling menu item functionality.
 *
 * @package Classy
 */

namespace Classy;

/**
 * Class Menu_Item.
 */
class Menu_Item extends Basis {

	/**
	 * Children.
	 *
	 * @var array
	 */
	protected $children = array();

	/**
	 * CSS Classes.
	 *
	 * @var array
	 */
	protected $classes = array();

	/**
	 * If item has child.
	 *
	 * @var boolean
	 */
	protected $has_child = false;

	/**
	 * Nesting level.
	 *
	 * @var integer
	 */
	public $level = 0;

	/**
	 * Item title.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Checks if provided arg is instance of WP_Post and inits it.
	 *
	 * @param \WP_Post $item WP_Post object.
	 */
	public function __construct( $item ) {
		if ( is_a( $item, '\WP_Post' ) ) {
			$this->import( $item );
			$this->filter_classes();
		}
	}

	/**
	 * Returns item title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Returns item slug.
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->post_name;
	}

	/**
	 * Returns item link (url).
	 *
	 * @return string
	 */
	public function get_link() {
		return $this->url;
	}

	/**
	 * Returns item children, if there are any.
	 *
	 * @return array
	 */
	public function get_children() {
		return $this->children;
	}

	/**
	 * Returns menu item classes.
	 *
	 * @return string
	 */
	public function get_classes() {
		return implode( ' ', $this->classes );
	}

	/**
	 * Adds css class to classes array.
	 *
	 * @param string $class_name CSS class name.
	 */
	public function add_class( $class_name ) {
		$this->classes[] = $class_name;
	}

	/**
	 * Adds child to current Menu_Item.
	 *
	 * @param Menu_Item $item Menu_Item object.
	 */
	public function add_child( $item ) {
		if ( ! $this->has_child ) {
			$this->add_class( 'menu-item-has-children' );
			$this->has_child = true;
		}

		$this->children[] = $item;
		$item->level = $this->level + 1;

		if ( $item->children ) {
			$this->update_child_levels();
		}
	}

	/**
	 * Applies filters for item classes.
	 */
	protected function filter_classes() {
		$this->classes = apply_filters( 'nav_menu_css_class', $this->classes, $this );
	}

	/**
	 * Updates children nesting level param.
	 *
	 * @return boolean
	 */
	protected function update_child_levels() {
		if ( is_array( $this->children ) ) {
			foreach ( $this->children as $child ) {
				$child->level = $this->level + 1;
				$child->update_child_levels();
			}

			return true;
		}

		return false;
	}
}
