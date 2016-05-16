<?php 

/**
 * Template Scope
 *
 * Loads the scope (content) for requested template
 */
class ClassyScope {

	protected static $common = null;

	public static $folder = 'scope';

	/**
	 * Returns request scope
	 * 
	 * @return array
	 */
	public static function get_scope($template_name = null) {

		$scope = self::require_scope('common');

		if (is_string($template_name)) {

			$scope = self::extend_scope($scope, $template_name);
			
		} else {

			$request = ClassyHierarchy::get_current_request();

			$file = ClassyHierarchy::get_available_file('scope', $request);

			$scope = self::extend_scope($scope, $file);

		}


		return $scope;

	}


	/**
	 * Extends Scope with scope that is defined in theme_name/scope folder
	 * 
	 * @return array
	 */
	public static function extend_scope($scope, $template_name) {

		$scope = array_merge($scope, self::require_scope($template_name));
		
		return $scope;

	}


	/**
	 * Returns Common Scope
	 * 
	 * @return array
	 */
	public static function get_common_scope() {
		
		if ( null === self::$common ) {

			self::$common = self::require_scope('common');

		}

		return self::$common;

	}

	/**
	 * Requires file's scope
	 * 
	 * @param  string $filename
	 * @return array
	 */
	public static function require_scope($filename) {

		$return = array();

		$file = THEME_PATH . 'scope/' . $filename . '.php';

		if ( file_exists($file) ) {

			require $file;
			
		}

		if ( isset($data) ) {
			
			$return = $data;

		}

		return $return;

	}

}