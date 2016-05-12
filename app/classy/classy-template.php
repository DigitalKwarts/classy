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
	 * @param  string $template
	 * @return string
	 */
	public static function get_theme_template_path($template) {

		return THEME_PATH . self::$theme_templates_folder . '/' . $template  . '/' . $template . '.blade.php';

	}

	/**
	 * Checks if template exists
	 * 
	 * @param  string $template 
	 * @return boolean           
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
	public static function get_query_template($type) {

		$templates = self::get_query_templates_list($type);

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
	private static function get_query_templates_list($type) {

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

		if ( is_404() && $template = self::get_query_template('404') ) :

		elseif ( is_search() && $template = self::get_query_template('search') ) :

		elseif ( is_front_page() && $template = self::get_query_template('front-page') ) :

		elseif ( is_home() && $template = self::get_query_template('home') ) :

		elseif ( is_post_type_archive() && $template = self::get_query_template('post_type_archive') ) :

		elseif ( is_tax() && $template = self::get_query_template('taxonomy') ) :

		elseif ( is_attachment() && $template = self::get_query_template('attachment') ) :

		elseif ( is_single() && $template = self::get_query_template('single') ) :

		elseif ( is_page() && $template = self::get_query_template('page') ) :

		elseif ( is_singular() && $template = self::get_query_template('singular') ) :

		elseif ( is_category() && $template = self::get_query_template('category') ) :

		elseif ( is_tag() && $template = self::get_query_template('tag') ) :

		elseif ( is_author() && $template = self::get_query_template('author') ) :

		elseif ( is_date() && $template = self::get_query_template('date') ) :

		elseif ( is_archive() && $template = self::get_query_template('archive') ) :

		elseif ( is_paged() && $template = self::get_query_template('paged') ) :

		else :
		
			$template = 'index';

		endif;


		return $template;

	}


	/**
	 * Returns current template, that will be used for template page render
	 * 
	 * @return string
	 */
	public static function get_current_template($current_page = null) {

		$current_page = $current_page ? $current_page : self::get_current_page();

		return self::get_query_template($current_page);

	}


	/**
	 * Main Function to perform page render
	 */
	public static function render() {

		$views = THEME_PATH . self::$theme_templates_folder;
		$cache = WP_CONTENT_DIR . '/templatecache';

		$current_page = self::get_current_page();

		$template_name = self::get_current_template($current_page);

		if ($template_name) {

			$template = self::get_theme_template_path($template_name);

			$scope = ClassyScope::get_scope($template_name);

			if ($template) {

				$renderer = new BladeRenderer($views, array('cache_path' => $cache));

				echo $renderer->render($template_name . '.' . $template_name, $scope);

			}
			
		} else {

			die("Can't find template for " . $current_page . " page");

		}
	}
}