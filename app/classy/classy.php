<?php 

/**
 * The core theme class.
 *
 *
 * @since      1.0.0
 * @package    Classy
 * @author     Andrew Tolochka <atolochka@gmail.com>
 */
class Classy {

	/**
	 * Singleton instance of plugin
	 *
	 * @var Classy
	 * @since  0.1.0
	 */
	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  0.1.0
	 * @return Classy A single instance of this class.
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

		$this->include_core_files();

		$this->include_models();

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
		$this->define( 'THEME_FRAMEWORK_PATH', THEME_PATH . 'app/' );
		$this->define( 'THEME_FRAMEWORK_DIR', THEME_DIR . 'app/' );
	}

	/**
	 * Include core files that are responsible for theme render
	 */
	private function include_core_files() {
		require_once THEME_PATH . 'vendor/autoload.php';

		// Basis Class
		require_once THEME_FRAMEWORK_PATH . 'classy/classy-basis.php';

		// Theme Config
		require_once THEME_FRAMEWORK_PATH . 'classy/classy-config.php';
	
		// Scope
		require_once THEME_FRAMEWORK_PATH . 'classy/classy-scope.php';

		// Template Loader
		require_once THEME_FRAMEWORK_PATH . 'classy/classy-template.php';

		// Helper functions
		require_once THEME_FRAMEWORK_PATH . 'classy/classy-helper.php';

		// Query Helper
		require_once THEME_FRAMEWORK_PATH . 'classy/classy-query-helper.php';

		// Menu
		require_once THEME_FRAMEWORK_PATH . 'classy/classy-menu.php';

		// Menu Item
		require_once THEME_FRAMEWORK_PATH . 'classy/classy-menu-item.php';

		// Comment
		require_once THEME_FRAMEWORK_PATH . 'classy/classy-comment.php';

		// Appearance
		require_once THEME_FRAMEWORK_PATH . 'appearance.php';
	}

	/**
	 * Include theme Object-Orienter models
	 */
	private function include_models() {
		$files = (array) glob( THEME_FRAMEWORK_PATH . '/models/*.php' );

		foreach ( $files as $filename ) {

			if ( !empty($filename) ) {

				require_once $filename;

			}

		}
	}

	/**
	 * Init Theme Configuration
	 */
	private function init_config() {
		$this->config = ClassyConfig::init();
	}

	/**
	 * Returns theme config variable
	 * 
	 * @param  string $name
	 * @return any
	 */
	public static function get_config_var($name) {
		$vars = ClassyConfig::get_vars();

		if (isset($vars[$name])) return $vars[$name];

		return false;
	}

	/**
	 * Returns theme textdomain
	 * 
	 * @return string
	 */
	public static function textdomain() {
		$textdomain = Classy::get_config_var('textdomain');

		return $textdomain ? $textdomain : THEME;
	}

	/**
	 * Performs template render. 
	 * If there is $template attribute presented, it will render requested template. 
	 * If it's not it will try to find necessary template based on $wp_query
	 * 
	 * @param  string|null $template template path in blade format, ex: single, base.default, single.partials.slider and etc
	 * @param  array|null  $data     Additional params
	 * @return void                
	 */
	public static function render($template = null, $data = null) {
		ClassyTemplate::render($template, $data);
	}

	/**
	 * Alias for ClassyHelper::get_archives_title()
	 * Returns page title for archive page. 
	 * Example: Archives, Author: John Doe, Tag: Lorem Ipsum
	 * 
	 * @return string
	 */
	public static function archives_title() {
		return ClassyHelper::get_archives_title();
	}


	/**
	 * Returns posts
	 * 
	 * @param  mixed $args        Array of query args
	 * @param  string  $return_type ClassyPost/object/id
	 * @return mixed               
	 */
	public static function get_posts($args = false, $return_type = 'ClassyPost') {
		$_return = array();

		$query = ClassyQueryHelper::find_query($args);

		if (isset($query->posts)) {

			foreach ($query->posts as $post_id) {
				
				if ($return_type == 'ClassyPost') {
				
					$_return[] = new ClassyPost($post_id);
				
				} elseif($return_type == 'object') {

					$_return[] = get_post($post_id);

				} else {

					$_return[] = $post_id;

				}

			}

		}

		return $_return;
	}

	/**
	 * Returns post
	 * 
	 * @param  mixed $args Array of query args
	 * @param  string  $return_type ClassyPost/object/id
	 * @return mixed               
	 */
	public static function get_post($args = false, $return_type = 'ClassyPost') {
		$posts = self::get_posts($args, $return_type);

		if ( $post = reset($posts ) ) {
			return $post;
		}
	}

	/**
	 * @param array   $prefs
	 * @return array mixed
	 */
	public static function get_pagination( $prefs = array() ) {
		global $wp_query;
		global $paged;
		global $wp_rewrite;

		$args = array();
		$args['total'] = ceil( $wp_query->found_posts / $wp_query->query_vars['posts_per_page'] );
		
		if ( $wp_rewrite->using_permalinks() ) {
			
			$url = explode( '?', get_pagenum_link( 0 ) );
			
			if ( isset( $url[1] ) ) {
				parse_str( $url[1], $query );
				$args['add_args'] = $query;
			}
			
			$args['format'] = 'page/%#%';
			$args['base'] = trailingslashit( $url[0] ).'%_%';

		} else {
			$big = 999999999;
			$args['base'] = str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) );
		}

		$args['type'] = 'array';
		$args['current'] = max( 1, get_query_var( 'paged' ) );
		$args['mid_size'] = max( 9 - $args['current'], 3 );
		$args['prev_next'] = false;
		
		if ( is_int( $prefs ) ) {
			$args['mid_size'] = $prefs - 2;
		} else {
			$args = array_merge( $args, $prefs );
		}

		$data = array();
		$data['pages'] = ClassyHelper::paginate_links( $args );
		$next = get_next_posts_page_link( $args['total'] );
		
		if ( $next ) {
			$data['next'] = array( 'link' => untrailingslashit( $next ), 'class' => 'page-numbers next' );
		}

		$prev = previous_posts( false );
		
		if ( $prev ) {
			$data['prev'] = array( 'link' => untrailingslashit( $prev ), 'class' => 'page-numbers prev' );
		}
		
		if ( $paged < 2 ) {
			$data['prev'] = null;
		}
		
		return ClassyHelper::array_to_object($data);
	}

}


/**
 * Grab the Classy object and return it.
 * Wrapper for Classy::get_instance()
 *
 * @since  0.1.0
 * @return Classy  Singleton instance of plugin class.
 */
function get_theme_framework() {
	return Classy::get_instance();
}

/**
 * Get Instance
 * 
 * @var classy
 */
$classy = get_theme_framework(); 