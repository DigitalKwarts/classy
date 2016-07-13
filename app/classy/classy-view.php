<?php

/**
 * View Loader
 *
 * Loads the corresponding template based on request
 */
class ClassyView {

	/**
	 * Views folder.
	 *
	 * @var string
	 */
	public static $folder = 'views';

	/**
	 * Returns view name to show based on request value.
	 *
	 * @return string
	 */
	public static function get_view() {

		$request = ClassyHierarchy::get_current_request();

		$file = ClassyHierarchy::get_available_file( 'view', $request );

		$view = self::get_blade_view( $file );

		return $view;

	}

	/**
	 * Replaces all slashes with dots.
	 *
	 * @param string $view View's name.
	 *
	 * @return string
	 */
	public static function get_blade_view( $view ) {

		return str_replace( '/', '.', $view );

	}

	/**
	 * Returns list of theme page templates.
	 *
	 * @return array
	 */
	public static function get_page_templates_list() {

		$templates = array();

		$files = (array) glob( CLASSY_THEME_PATH . '/' . self::$folder . '/*/*.blade.php' );

		foreach ( $files as $filename ) {

			if ( ! empty( $filename ) ) {

				if ( ! preg_match( '/\{\{\-\-\s*Template Name:(.*)\s*\-\-\}\}/mi', file_get_contents( $filename ), $header ) ) { continue; }

				$template_name = trim( $header[1] );

				preg_match( '/\/([^\/]*)\.blade.php$/is', $filename, $filename_match );

				$template_file = 'classy-' . $filename_match[1];

				$templates[ $template_file ] = $template_name;

			}
		}

		return $templates;

	}
}
