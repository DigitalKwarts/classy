<?php

/**
 * View's scope.
 *
 * Loads the scope (data).
 */
class ClassyScope {

	protected static $common = null;

	public static $folder = 'scope';

	/**
	 * Returns request scope.
	 *
	 * @return array
	 */

	/**
	 * @todo: Write description here.
	 *
	 * @param string|null $view_name View's name.
	 *
	 * @return array
	 */
	public static function get_scope( $view_name = null ) {

		$scope = self::get_common_scope();

		if ( is_string( $view_name ) ) {

			$scope = self::extend_scope( $scope, $view_name );

		} else {

			$request = ClassyHierarchy::get_current_request();

			$file = ClassyHierarchy::get_available_file( 'scope', $request );

			$scope = self::extend_scope( $scope, $file );

		}

		return $scope;

	}

	/**
	 * Extends Scope with scope that is defined in theme_name/scope folder.
	 *
	 * @param array  $scope
	 * @param string $view_name View's name.
	 *
	 * @return array
	 */
	public static function extend_scope( $scope, $view_name ) {

		$scope = array_merge( $scope, self::require_scope( $view_name ) );

		return $scope;

	}

	/**
	 * Returns Common Scope.
	 *
	 * @return array
	 */
	public static function get_common_scope() {

		if ( null === self::$common ) {

			self::$common = self::require_scope( 'common' );

		}

		return self::$common;

	}

	/**
	 * Requires file's scope.
	 *
	 * @param  string $filename View's file name.
	 *
	 * @return array
	 */
	public static function require_scope( $filename ) {

		$_return = array();

		$file = ClassyHierarchy::get_file_path( 'scope', $filename );

		if ( file_exists( $file ) ) {

			require $file;

		}

		if ( isset( $data ) ) {

			$_return = $data;

		}

		return $_return;

	}
}
