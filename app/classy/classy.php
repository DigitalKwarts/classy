<?php

use Windwalker\Renderer\BladeRenderer;


/**
 * The core theme class.
 *
 * @since 	1.0.0
 * @package Classy
 * @author 	Andrew Tolochka <atolochka@gmail.com>
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

		add_filter( 'theme_page_templates', array( $this, 'filter_templates' ) );

	}

	/**
	 * Defines plugin constants
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_constants() {

		$theme = wp_get_theme();

		define( 'CLASSY_THEME', $theme->template );
		define( 'CLASSY_THEME_NAME', $theme->get( 'Name' ) );
		define( 'CLASSY_THEME_PATH', get_template_directory() . '/' );
		define( 'CLASSY_THEME_DIR', get_template_directory_uri() . '/' );
		define( 'CLASSY_THEME_VERSION', $theme->get( 'Version' ) );
		define( 'CLASSY_THEME_FRAMEWORK_PATH', CLASSY_THEME_PATH . 'app/' );
		define( 'CLASSY_THEME_FRAMEWORK_DIR', CLASSY_THEME_DIR . 'app/' );

	}

	/**
	 * Include core files that are responsible for theme render
	 */
	private function include_core_files() {

		require_once CLASSY_THEME_PATH . 'vendor/autoload.php';

		// Basis Class.
		require_once CLASSY_THEME_FRAMEWORK_PATH . 'classy/classy-basis.php';

		// Hierarchy.
		require_once CLASSY_THEME_FRAMEWORK_PATH . 'classy/classy-hierarchy.php';

		// Theme Config.
		require_once CLASSY_THEME_FRAMEWORK_PATH . 'classy/classy-config.php';

		// Scope.
		require_once CLASSY_THEME_FRAMEWORK_PATH . 'classy/classy-scope.php';

		// View Loader.
		require_once CLASSY_THEME_FRAMEWORK_PATH . 'classy/classy-view.php';

		// Helper functions.
		require_once CLASSY_THEME_FRAMEWORK_PATH . 'classy/classy-helper.php';

		// Query Helper.
		require_once CLASSY_THEME_FRAMEWORK_PATH . 'classy/classy-query-helper.php';

		// Menu.
		require_once CLASSY_THEME_FRAMEWORK_PATH . 'classy/classy-menu.php';

		// Menu Item.
		require_once CLASSY_THEME_FRAMEWORK_PATH . 'classy/classy-menu-item.php';

		// Comment.
		require_once CLASSY_THEME_FRAMEWORK_PATH . 'classy/classy-comment.php';

		// Appearance.
		require_once CLASSY_THEME_FRAMEWORK_PATH . 'appearance.php';

	}

	/**
	 * Include theme Object-Oriented models.
	 */
	private function include_models() {

		$files = (array) glob( CLASSY_THEME_FRAMEWORK_PATH . '/models/*.php' );

		foreach ( $files as $filename ) {

			if ( ! empty( $filename ) ) {

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
	 * Filters registered templates and adds custom theme templates.
	 *
	 * @param array $page_templates Available WordPress templates.
	 *
	 * @return array
	 */
	public function filter_templates( $page_templates = array() ) {

		$custom_templates = ClassyView::get_page_templates_list();

		return array_merge( $page_templates, $custom_templates );

	}

	/**
	 * Returns theme config variable.
	 *
	 * @param string $name Variable's name.
	 *
	 * @return mixed|bool Return false if variable not found.
	 */
	public static function get_config_var( $name ) {

		$vars = ClassyConfig::get_vars();

		return ( isset( $vars[ $name ] ) ) ? $vars[ $name ] : false;

	}

	/**
	 * Returns theme textdomain
	 *
	 * @return string
	 */
	public static function textdomain() {

		$textdomain = Classy::get_config_var( 'textdomain' );

		return $textdomain ? $textdomain : CLASSY_THEME;

	}

	/**
	 * Performs view render.
	 * If there is $view attribute presented, it will render requested view.
	 * If it's not it will try to find necessary view based on $wp_query
	 *
	 * @param  string|null $view View path in blade format, ex: single, layout.default, single.partials.slider and etc.
	 * @param  array|null  $data Additional params.
	 * @return void
	 */
	public static function render( $view = null, $data = null ) {

		$views = CLASSY_THEME_PATH . ClassyView::$folder;
		$cache = WP_CONTENT_DIR . '/viewcache';
		$common_scope = ClassyScope::get_common_scope();

		if ( null !== $view && is_string( $view ) ) {

			if ( $data && is_array( $data ) ) {

				$scope = array_merge( $common_scope, $data );

			} else {

				$scope = $common_scope;

			}
		} else {

			$view = ClassyView::get_view();

			$scope = ClassyScope::get_scope();

		}

		$renderer = new BladeRenderer( $views, array( 'cache_path' => $cache ) );

		echo $renderer->render( $view, $scope ); // XSS: xss ok.

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
	 * @param  mixed  $args   Array of query args.
	 * @param  string $return object/id/ClassyPost.
	 *
	 * @return array
	 */
	public static function get_posts( $args = false, $return = 'ClassyPost' ) {

		$_return = array();

		$query = ClassyQueryHelper::find_query( $args );

		if ( isset( $query->posts ) ) {

			foreach ( $query->posts as $post ) {

				if ( 'id' === $return ) {

					$_return[] = $post->id;

				} elseif ( 'object' === $return ) {

					$_return[] = $post;

				} elseif ( class_exists( $return ) ) {

					$_return[] = new $return( $post );

				}
			}
		}

		return $_return;
	}


	/**
	 * Returns post.
	 *
	 * @param  mixed  $args 		Array of query args.
	 * @param  string $return_type 	ClassyPost/object/id.
	 *
	 * @return mixed
	 */
	public static function get_post( $args = false, $return_type = 'ClassyPost' ) {

		$posts = self::get_posts( $args, $return_type );

		if ( $post = reset( $posts ) ) {
			return $post;
		}

	}

	/**
	 * @todo: Write description here.
	 *
	 * @param array $prefs Args for paginate_links.
	 *
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

		return ClassyHelper::array_to_object( $data );

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
