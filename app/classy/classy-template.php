<?php 

use Windwalker\Renderer\BladeRenderer;

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
	public static $theme_templates_folder = 'views';


	/**
	 * Return theme template absolute path
	 * 
	 * @param  string $template in blade path format, ex: base.header
	 * @return string full path to template file
	 */
	public static function get_theme_template_path($template) {

		$template = self::get_nested_blade_path($template);

		$template = str_replace('.', '/', $template);

		return THEME_PATH . self::$theme_templates_folder . '/' . $template . '.blade.php';			

	}

	/**
	 * Checks if template exists
	 * 
	 * @param  string $template in blade path format, ex: base.header
	 * @return boolean true/false
	 */
	public static function template_exists($template) {

		$template_path = self::get_theme_template_path($template);

		return file_exists($template_path);

	}


	/**
	 * Returns template name for render, based on type of request
	 * 
	 * @param  string $type 
	 * @return array 
	 */
	public static function get_available_template($type) {

		$templates = self::get_request_templates_list($type);

		foreach ($templates as $template) {

			if ( self::template_exists($template) ):

				return $template;

			endif;

		}

		return false;

	}


	/**
	 * Returns list of templates to check, based on type of request
	 * 
	 * @param  string $type 
	 * @return array
	 */
	private static function get_request_templates_list($type) {

		$templates = array();


		// Home

		if ( $type == 'home' ) :

			$templates[] = 'home';
			$templates[] = 'index';


		// Single

		elseif ( $type == 'single' ) :

			$post_type = get_query_var( 'post_type' );

			$templates[] = 'single-' . $post_type;

			$templates[] = 'single';

		// Post type

		elseif ( $type == 'post_type_archive' ) :

			$post_type = get_query_var( 'post_type' );

			$templates[] = 'archive-' . $post_type;

			$templates[] = 'archive';


		// Taxonomy

		elseif ( $type == 'taxonomy' ):

			$term = get_queried_object();

			if ( ! empty( $term->slug ) ) {
				
				$taxonomy = $term->taxonomy;

				$templates[] = "taxonomy-$taxonomy-{$term->slug}";
				$templates[] = "taxonomy-$taxonomy";

			}

			$templates[] = 'taxonomy';

		// Category

		elseif ( $type == 'category' ):

			$category = get_queried_object();

			if ( ! empty( $category->slug ) ) {
				$templates[] = "category-{$category->slug}";
				$templates[] = "category-{$category->term_id}";
			}
			$templates[] = 'category';


		// Attachment

		elseif ( $type == 'attachment' ):

			$attachment = get_queried_object();

			if ( $attachment ) {

				if ( false !== strpos( $attachment->post_mime_type, '/' ) ) {
				
					list( $type, $subtype ) = explode( '/', $attachment->post_mime_type );
				
				} else {
				
					list( $type, $subtype ) = array( $attachment->post_mime_type, '' );
				
				}

				if ( ! empty( $subtype ) ) {
					$templates[] = "{$type}-{$subtype}";
					$templates[] = "{$subtype}";
				}
				$templates[] = "{$type}";

			}
			$templates[] = 'attachment';


		// Tag

		elseif ( $type == 'tag' ):

			$tag = get_queried_object();

			if ( ! empty( $tag->slug ) ) {
				$templates[] = "tag-{$tag->slug}";
				$templates[] = "tag-{$tag->term_id}";
			}
			$templates[] = 'tag';


		// Author

		elseif ( $type == 'author' ):

			$author = get_queried_object();

			if ( $author instanceof WP_User ) {
				$templates[] = "author-{$author->user_nicename}";
				$templates[] = "author-{$author->ID}";
			}
			$templates[] = 'author';


		// Front Page

		elseif ( $type == 'front-page' ):

			$id = get_queried_object_id();

			$pagename = get_query_var('pagename');

			if ( ! $pagename && $id ) {
				// If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object
				$post = get_queried_object();
				if ( $post )
					$pagename = $post->post_name;
			}

			$template = get_post_meta('theme-page-template', $id);

			if ( $template != 'index' )
				$templates[] = $template;
			if ( $pagename )
				$templates[] = "page-$pagename";
			if ( $id )
				$templates[] = "page-$id";
			$templates[] = '';

		// Page

		elseif ( $type == 'page' ):

			$id = get_queried_object_id();
			
			$template = get_post_meta('theme-page-template', $id);

			$pagename = get_query_var('pagename');

			if ( ! $pagename && $id ) {
				// If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object
				$post = get_queried_object();
				if ( $post )
					$pagename = $post->post_name;
			}

			if ( $template != 'index' )
				$templates[] = $template;
			if ( $pagename )
				$templates[] = "page-$pagename";
			if ( $id )
				$templates[] = "page-$id";
			$templates[] = 'page';


		// Default

		else:

			$templates[] = $type;

		endif;


		return $templates;

	}


	/**
	 * Returns current page template slug
	 * 
	 * @return string
	 */
	public static function get_current_page() {

		if ( is_404() && $template = self::get_available_template('404') ) :

		elseif ( is_search() && $template = self::get_available_template('search') ) :

		elseif ( is_front_page() && $template = self::get_available_template('front-page') ) :

		elseif ( is_home() && $template = self::get_available_template('home') ) :

		elseif ( is_post_type_archive() && $template = self::get_available_template('post_type_archive') ) :

		elseif ( is_tax() && $template = self::get_available_template('taxonomy') ) :

		elseif ( is_attachment() && $template = self::get_available_template('attachment') ) :

		elseif ( is_single() && $template = self::get_available_template('single') ) :

		elseif ( is_page() && $template = self::get_available_template('page') ) :

		elseif ( is_singular() && $template = self::get_available_template('singular') ) :

		elseif ( is_category() && $template = self::get_available_template('category') ) :

		elseif ( is_tag() && $template = self::get_available_template('tag') ) :

		elseif ( is_author() && $template = self::get_available_template('author') ) :

		elseif ( is_date() && $template = self::get_available_template('date') ) :

		elseif ( is_archive() && $template = self::get_available_template('archive') ) :

		elseif ( is_paged() && $template = self::get_available_template('paged') ) :

		else :
		
			$template = 'index';

		endif;


		return $template;

	}

	/**
	 * Modifies template path to nested that we use for our template structuring
	 * 
	 * @param  string $template ex: single
	 * @return string           single.single (it will look at "single" folder and will find "single.blade.php" template)
	 */
	public static function get_nested_blade_path($template) {

		if (preg_match('/\./', $template)) {

			return $template;	

		} else {

			return $template . '.' . $template;
			
		}

	}


	/**
	 * Returns available template, based on page argument
	 * 
	 * @return string
	 */
	public static function get_blade_template($page = null) {

		if (!$page) {
			$page = self::get_current_page();
		}


		$template = self::get_available_template($page);

		if ($template) {

			return self::get_nested_blade_path($template);
			
		}

		return false;

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

		$views = THEME_PATH . self::$theme_templates_folder;
		$cache = WP_CONTENT_DIR . '/templatecache';
		$common_scope = ClassyScope::get_common_scope();

		if ($template !== null && is_string($template)) {

			if ($data && is_array($data)) {

				$scope = array_merge($common_scope, $data);

			} else {

				$scope = $common_scope;

			}

		} else {

			$current_page = self::get_current_page();

			$template = self::get_blade_template($current_page);

			$scope = ClassyScope::get_scope($current_page);

		}

		$renderer = new BladeRenderer($views, array('cache_path' => $cache));

		echo $renderer->render($template, $scope);

	}
	
}