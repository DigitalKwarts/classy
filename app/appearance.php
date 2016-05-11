<?php 

/**
 * Theme Appearance Class
 *
 * Manages JS & CSS enqueuing of the theme
 */
class ClassyAppearance {

	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'wp_print_scripts', array($this, 'init_js_vars') );

	}

	/**
	 * Enqueues styles
	 */
	public function enqueue_styles() {

		wp_register_style( 'flotheme_general_css', THEME_DIR . 'assets/css/general.css', array(), THEME_VERSION, 'all' );
		
		wp_enqueue_style( 'flotheme_general_css' );

	}

	/**
	 * Enqueues scripts
	 */
	public function enqueue_scripts() {

		wp_deregister_script( 'jquery' );
		
		wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js', array(), THEME_VERSION, true );

		if ( Classy::get_config_var('environment') == 'production' ) {
		
			wp_register_script( 'theme_plugins', THEME_DIR . 'assets/js/min/production.js', array( 'jquery' ), THEME_VERSION, true );
		
		} else {
			
			wp_register_script( 'theme_plugins', THEME_DIR . 'assets/js/plugins.js', array( 'jquery' ), THEME_VERSION, true );

			wp_register_script( 'theme_scripts', THEME_DIR . 'assets/js/scripts.js', array( 'jquery' ), THEME_VERSION, true );
		
		}

		wp_enqueue_script( 'theme_plugins' );
		
		wp_enqueue_script( 'theme_scripts' );

		wp_enqueue_script( 'theme_production' );

	}

	/**
	 * Load needed options & translations into template.
	 */
	public function init_js_vars() {
	
		$options = array(
			'base_url'          => home_url(''),
			'blog_url'          => home_url('archives/'),
			'template_dir'      => THEME_DIR,
			'ajax_load_url'     => site_url('/wp-admin/admin-ajax.php'),
			'is_mobile'         => (int) wp_is_mobile(),
		);

		wp_localize_script(
			'theme_plugins',
			'theme',
			$options
		);

	}

}

new ClassyAppearance();