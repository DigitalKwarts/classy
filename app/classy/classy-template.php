<?php 

/**
 * Template Loader
 *
 * Loads the corresponding template based on request
 */
class ClassyTemplate {

	/**
	 * Theme twig templates folder
	 * 
	 * @var string
	 */
	public static $folder = 'views';

	/**
	 * Returns template to show based on request value
	 * 
	 * @return string
	 */
	public static function get_template() {

		$request = ClassyHierarchy::get_current_request();

		$file = ClassyHierarchy::get_available_file('template', $request);

		$template = self::get_blade_template($file);

		return $template;

	}


	/**
	 * Replaces all slashes with dots
	 * 
	 * @param  string $template
	 * @return string           
	 */
	public static function get_blade_template($template) {

		return str_replace('/', '.', $template);

	}

	
	/**
	 * Returns list of theme page templates
	 * 
	 * @return array
	 */
	public static function get_page_templates_list() {

		$templates = array();
		
	    $files = (array) glob( THEME_PATH . '/' . self::$folder . '/*/*.blade.php' );

		foreach ( $files as $filename ) {
			
			if ( !empty($filename) ) {

				if ( ! preg_match( '/\{\{\-\-\s*Template Name:(.*)\s*\-\-\}\}/mi', file_get_contents( $filename ), $header ) ) continue;

				$template_name = trim($header[1]);

				preg_match('/\/([^\/]*)\.blade.php$/is', $filename, $filename_match);

				$template_file = 'classy-' . $filename_match[1];

				$templates[$template_file] = $template_name;
				
			}

		}

		return $templates;

	}

}