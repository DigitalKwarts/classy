<?php

/**
 * Class for handling menu functionality
 */

class ClassyMenu {

	/**
	 * Menu ID
	 * @var int
	 */
	public $ID;

	/**
	 * Items
	 * @var array
	 */
	protected $items;

	/**
	 * Main constructor. Tries to find menu id based on provided arg (or not) and inits menu
	 *
	 * @param string $arg it can be menu id, slug or full name
	 */
	public function __construct( $arg = null ) {

		if ( is_numeric( $arg ) && 0 != $arg ) {

			$menu_id = $this->check_menu_id( $arg );

		} elseif ( is_string( $arg ) ) {

			$menu_id = $this->get_menu_id_by_name( $arg );

		}

		if ( ! isset( $menu_id ) ) {

			$menu_id = $this->get_first_menu_id();

		}

		if ( $menu_id ) {

			$this->ID = $menu_id;

			$this->init();

		}

	}

	/**
	 * Inits menu
	 *
	 * @return void
	 */
	protected function init() {

		$_return = array();

		$items = wp_get_nav_menu_items( $this->ID );

		foreach ( $items as $item ) {

			$_return[ $item->ID ] = new ClassyMenuItem( $item );
		}

		// Apply nesting

		foreach ( $_return as $item_id => $item ) {

			if ( isset( $item->menu_item_parent ) && $item->menu_item_parent && isset( $_return[ $item->menu_item_parent ] ) ) {

				$_return[ $item->menu_item_parent ]->add_child( $item );

				unset( $_return[ $item_id ] );

			}
		}

		$this->items = $_return;

	}

	/**
	 * Retuns first menu id or false if there are no menus
	 *
	 * @return int
	 */
	protected function get_first_menu_id() {

		$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );

		if ( is_array( $menus ) && count( $menus ) ) {

			if ( isset( $menus[0]->term_id ) ) {

				return $menus[0]->term_id;

			}
		}

		return false;

	}

	/**
	 * Checks if the provided menu id exists
	 *
	 * @param  int $menu_id
	 * @return int|boolean
	 */
	protected function check_menu_id( $menu_id ) {

		$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );

		if ( is_array( $menus ) && count( $menus ) ) {

			foreach ( $menus as $menu ) {

				if ( $menu->term_id == $menu_id ) {

					return $menu_id;

				}
			}
		}

		return false;

	}

	/**
	 * Returns menu id by menu name/slug
	 *
	 * @param  string $slug
	 * @return int
	 */
	protected function get_menu_id_by_name( $slug = null ) {

		if ( $slug && is_string( $slug ) ) {

			$menu_id = get_term_by( 'slug', $slug, 'nav_menu' );

			if ( $menu_id ) { return $menu_id; }

			$menu_id = get_term_by( 'name', $slug, 'nav_menu' );

			if ( $menu_id ) { return $menu_id; }
		}

		return false;

	}

	/**
	 * Returns menu items
	 *
	 * @return array
	 */
	public function get_items() {

		return $this->items;

	}
}
