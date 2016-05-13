<?php

/**
 * Class for handling menu item functionality
 */

class ClassyMenuItem extends ClassyBasis {

	public $children = array();

	public $classes = array();

	public $has_child_class = false;

	public $level = 0;
	
	public $title;

	public function __construct($item) {

		if (is_a($item, 'WP_Post')) {

			$this->import($item);
			$this->import_classes($item);
			
		}

	}

	public function get_title() {

		return $this->title;

	}

	public function get_slug() {

		return $this->post_name;

	}

	public function get_link() {

		return $this->url;

	}

	public function get_children() {

		return $this->children;

	}


	public function add_class($class_name) {
		
		$this->classes[] = $class_name;
		$this->class .= ' ' . $class_name;

	}

	public function add_child($item) {

		if ( !$this->has_child_class ) {
			$this->add_class( 'menu-item-has-children' );
			$this->has_child_class = true;
		}

		if ( !isset( $this->children ) ) {
			$this->children = array();
		}

		$this->children[] = $item;
		$item->level = $this->level + 1;

		if ($item->children) {
			$this->update_child_levels();
		}

	}

	protected function import_classes( $data ) {
	
		if ( is_array($data) ) {
			$data = (object) $data;
		}
	
		$this->classes = array_merge( $this->classes, $data->classes );
		$this->classes = array_unique( $this->classes );
		$this->classes = apply_filters( 'nav_menu_css_class', $this->classes, $this );
	
	}


	protected function update_child_levels() {

		if (is_array($this->children)) {
		
			foreach( $this->children as $child ) {
				$child->level = $this->level + 1;
				$child->update_child_levels();
			}
		
			return true;
		
		}

	}

}