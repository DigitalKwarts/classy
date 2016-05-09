<?php 

/**
 * The core theme class.
 *
 *
 * @since      1.0.0
 * @package    ThemeFramework
 * @author     Andrew Tolochka <atolochka@gmail.com>
 */
class ThemeFramework {

	/**
	 * Singleton instance of plugin
	 *
	 * @var ThemeFramework
	 * @since  0.1.0
	 */
	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  0.1.0
	 * @return ThemeFramework A single instance of this class.
	 */
	public static function get_instance() {

		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;

	}

	/**
	 * Define the core functionality of the them.
	 *
	 * Set the theme name and the theme version that can be used throughout the theme.
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {

		$this->define_constants();

		$this->includes();

		$this->init_config();

	}

	/**
	 * Function to define constants
	 * 
	 * @param  string
	 * @param  string
	 */
	private function define( $name, $value ) {
		
		if ( !defined($name) ) {
			
			define( $name, $value );

		}

	}

	/**
	 * Defines plugin constants
	 * 
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_constants() {

		$theme = wp_get_theme();

		$this->define( 'THEME', $theme->template );
		$this->define( 'THEME_NAME', $theme->get('Name') );
		$this->define( 'THEME_PATH', get_template_directory() . '/' );
		$this->define( 'THEME_DIR', get_template_directory_uri() . '/' );
		$this->define( 'THEME_VERSION', $theme->get('Version') );
		$this->define( 'THEME_FRAMEWORK_PATH', THEME_PATH . 'framework/' );
		$this->define( 'THEME_FRAMEWORK_DIR', THEME_DIR . 'framework/' );

	}

	public function includes() {

		require 'vendor/autoload.php';

		// Theme Config
		require_once THEME_FRAMEWORK_PATH . 'core/theme-config.php';
	
		// Scope
		require_once THEME_FRAMEWORK_PATH . 'core/scope.php';

		// Template Loader
		require_once THEME_FRAMEWORK_PATH . 'core/template-loader.php';

		// Appearance
		require_once THEME_PATH . 'custom/appearance.php';

	}

	/**
	 * Init Theme Configuration
	 */
	private function init_config() {

		$this->config = ThemeConfig::init();

	}

	/**
	 * Returns theme config variable
	 * 
	 * @param  string $name
	 * @return any
	 */
	public static function get_config_var($name) {

		$vars = ThemeConfig::get_vars();

		if (isset($vars[$name])) return $vars[$name];

		return false;

	}

	/**
	 * Returns theme textdomain
	 * 
	 * @return string
	 */
	public static function textdomain() {

		$textdomain = ThemeFramework::get_config_var('textdomain');

		return $textdomain ? $textdomain : THEME;

	}

	/**
	 * Main Template Render Function
	 */
	public static function render() {

		TemplateLoader::render();

	}

}


/**
 * Grab the ThemeFramework object and return it.
 * Wrapper for ThemeFramework::get_instance()
 *
 * @since  0.1.0
 * @return ThemeFramework  Singleton instance of plugin class.
 */
function get_theme_framework() {
	
	return ThemeFramework::get_instance();

}

/**
 * Get Instance
 * 
 * @var ThemeFramework
 */
$themeframework = get_theme_framework(); 