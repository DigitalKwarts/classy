<?php

class ClassyImage extends ClassyBasis {

	/**
	 * Current image id
	 * @var int
	 */
	public $ID;

	/**
	 * Main constructor function. Requires image id
	 * @param int $pid
	 */
	public function __construct( $pid = null ) {

		// Checks if image with this id exists
		if ( null !== $pid && wp_get_attachment_image_src( $pid ) ) {

			$this->ID = $pid;

		} else {

			$this->ID = 0;

		}
	}

	/**
	 * Returns default image url
	 * @return string
	 */
	public static function get_default_image() {

		// You can put here any url
		return THEME_DIR . '/assets/noimage.png';

	}

	/**
	 * Returns image url. In case image ID is 0 or not set returns default image
	 * @param  string $size
	 * @return string
	 */
	public function src( $size = 'medium' ) {

		if ( $this->ID ) {

			$thumb = wp_get_attachment_image_src( $this->ID, $size );

			return $thumb[0];

		} else {

			return self::get_default_image();

		}
	}
}
