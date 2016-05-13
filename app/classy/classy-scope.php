<?php 

/**
 * Template Scope
 *
 * Loads the scope (content) for requested template
 */
class ClassyScope {

	protected static $common = null;

	/**
	 * Returns basic scope
	 * 
	 * @return array
	 */
	public static function get_scope($template_name = null) {

		global $paged;

		$scope = self::require_scope('common');

		if ($template_name) {

			$scope = self::extend_scope($scope, $template_name);
			
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