<?php

/**
 * Class for handling menu functionality
 */

class ClassyMenu {

	public $ID;

	public $items;

	public function __construct($arg = null) {

		if (is_numeric($arg) && $arg != 0) {

			$menu_id = $this->check_menu_id($arg);

		} elseif (is_string($arg)) {

			$menu_id = $this->get_menu_id_by_name($arg);

		}

		if (!isset($menu_id)) {
			
			$menu_id = $this->get_first_menu_id();

		}

		if ($menu_id) {

			$this->ID = $menu_id;

			$this->init();

		}

	}

	protected function init() {

		$_return = array();

		$items = wp_get_nav_menu_items($this->ID);

		foreach ($items as $item) {

			$_return[$item->ID] = new ClassyMenuItem($item);
		}

		// Apply nesting

		foreach ($_return as $item_id => $item) {
			
			if (isset($item->menu_item_parent) && $item->menu_item_parent && isset($_return[$item->menu_item_parent])) {
			
				$_return[$item->menu_item_parent]->add_child($item);

				unset($_return[$item_id]);
			
			}

		}

		$this->items = $_return;

	}

	protected function get_first_menu_id() {

		$menus = get_terms('nav_menu', array('hide_empty' => true));
		
		if (is_array($menus) && count($menus)) {

			if (isset($menus[0]->term_id)) {

				return $menus[0]->term_id;
			
			}
		
		}

		return false;

	}


	protected function check_menu_id($menu_id) {

		$menus = get_terms('nav_menu', array('hide_empty' => true));
		
		if (is_array($menus) && count($menus)) {

			foreach ($menus as $menu) {

				if ($menu->term_id == $menu_id) {

					return $menu_id;

				}

			}
		
		}

		return false;

	}

	protected function get_menu_id_by_name($slug = 0) {

		if (is_string($slug) && !is_numeric($slug)) {
			
			$menu_id = get_term_by('slug', $slug, 'nav_menu');

			if ($menu_id) return $menu_id;

			$menu_id = get_term_by('name', $slug, 'nav_menu');
			
			if ($menu_id) return $menu_id;

		}

		return false;
	
	}

	public function get_items() {

		return $this->items;

	}


}