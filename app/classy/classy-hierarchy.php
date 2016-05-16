<?php

/**
 * Implements Wordpress Hierarchy 
 */

class ClassyHierarchy {

	/**
	 * Stores current request for multiple use
	 * 
	 * @var string/null
	 */
	protected static $current_request = null;


	/**
	 * Protected function to get current request type
	 * 
	 * @return string
	 */
	protected static function check_request() {

		if ( is_404()) : return '404';

		elseif ( is_search() ) : return 'search';

		elseif ( is_front_page() ) : return 'front-page';

		elseif ( is_home() ) : return 'home';

		elseif ( is_post_type_archive() ) : return 'post_type_archive';

		elseif ( is_tax() ) : return 'taxonomy';

		elseif ( is_attachment() ) : return 'attachment';

		elseif ( is_single() ) : return 'single';

		elseif ( self::is_classy_template() ) : return 'classy-template';

		elseif ( is_page() ) : return 'page';

		elseif ( is_singular() ) : return 'singular';

		elseif ( is_category() ) : return 'category';

		elseif ( is_tag() ) : return 'tag';

		elseif ( is_author() ) : return 'author';

		elseif ( is_date() ) : return 'date';

		elseif ( is_archive() ) : return 'archive';

		elseif ( is_paged() ) : return 'paged';

		else :
		
			return 'index';

		endif;

	}

	/**
	 * Returns current request type
	 * 
	 * @return string
	 */
	public static function get_current_request() {

		if ( null === self::$current_request ) {

			self::$current_request = self::check_request();

		}

		return self::$current_request;

	}


	/**
	 * Returns file's absolute path
	 * 
	 * @param  string $type     template/scope
	 * @param  string $template path to template ex: "post/archive"
	 * @return string           full fule path
	 */
	public static function get_file_path($type = 'template', $template) {

		if ($type == 'template') {

			$folder = ClassyTemplate::$folder;

			return THEME_PATH . $folder . '/' . $template . '.blade.php';			
		
		} elseif ($type == 'scope') {

			$folder = ClassyScope::$folder;

			return THEME_PATH . $folder . '/' . $template . '.php';			

		}

	}


	/**
	 * Checks if template exists
	 * 
	 * @param  string $type template/scope
	 * @param  string $template in blade path format, ex: layout/header
	 * @return boolean true/false
	 */
	public static function file_exists($type = 'template', $file) {

		$file = str_replace('.', '/', $file);

		$file_path = self::get_file_path($type, $file);

		return file_exists($file_path);

	}


	/**
	 * Returns template name for render, based on type of request
	 *
	 * @param  string $type template/scope
	 * @param  string $type 
	 * @return array 
	 */
	public static function get_available_file($type = 'template', $page) {

		$templates = self::get_request_templates_list($page);

		foreach ($templates as $template) {

			if ( self::file_exists($type, $template) ):

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

		// Single

		elseif ( $type == 'single' ) :

			$post_type = get_post_type();

			$templates[] = $post_type . '.single';

			$templates[] = 'single';

		// Post type

		elseif ( $type == 'post_type_archive' ) :

			$post_type = get_post_type();

			$templates[] = $post_type . '.archive';

			$templates[] = 'archive';


		// Taxonomy

		elseif ( $type == 'taxonomy' ):

			$term = get_queried_object();

			if ( ! empty( $term->slug ) ) {
				
				$taxonomy = $term->taxonomy;

				$templates[] = "taxonomy.$taxonomy-{$term->slug}";
				$templates[] = "taxonomy.$taxonomy";

			}

			$templates[] = 'taxonomy.taxonomy';

			$templates[] = 'taxonomy';

		// Category

		elseif ( $type == 'category' ):

			$category = get_queried_object();

			if ( ! empty( $category->slug ) ) {
				$templates[] = "category.{$category->slug}";
				$templates[] = "category.{$category->term_id}";
			}

			$templates[] = 'category.category';

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
					$templates[] = "attachment.{$type}.{$subtype}";
					$templates[] = "attachment.{$subtype}";

					$templates[] = "{$type}.{$subtype}";
					$templates[] = "{$subtype}";
				}

				$templates[] = "attachment.{$type}";
				$templates[] = "{$type}";

			}

			$templates[] = 'attachment.attachment';

			$templates[] = 'attachment';


		// Tag

		elseif ( $type == 'tag' ):

			$tag = get_queried_object();

			if ( ! empty( $tag->slug ) ) {
				$templates[] = "post.tag.{$tag->slug}";
				$templates[] = "post.tag.{$tag->term_id}";

				$templates[] = "tag.{$tag->slug}";
				$templates[] = "tag.{$tag->term_id}";
			}
			$templates[] = 'post.tag';

			$templates[] = 'tag';


		// Author

		elseif ( $type == 'author' ):

			$author = get_queried_object();

			if ( $author instanceof WP_User ) {
				$templates[] = "post.author.{$author->user_nicename}";
				$templates[] = "post.author.{$author->ID}";

				$templates[] = "author.{$author->user_nicename}";
				$templates[] = "author.{$author->ID}";
			}

			$templates[] = 'post.author';

			$templates[] = 'author';


		// Front Page

		elseif ( $type == 'front-page' ):

			$templates[] = 'front-page.front-page';
			$templates[] = 'front-page';
			
			$templates[] = 'home.home';
			$templates[] = 'home';

			$templates = array_merge($templates, self::get_request_templates_list('post_type_archive'));

		// Page

		elseif ( $type == 'classy-template' ):

			$template = self::get_classy_template();

			$templates[] = $template;

			$templates[] = 'page.' . $template;

			$templates[] = 'template.' . $template;


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

			if ( $template && $template != 'index' )
				$templates[] = $template;
			if ( $pagename )
				$templates[] = "page.$pagename";
			if ( $id )
				$templates[] = "page.$id";

			$templates[] = 'page.page';
			
			$templates[] = 'page';


		// Default

		else:

			$templates[] = $type;

		endif;

		$templates[] = 'index';

		return $templates;

	}


	/**
	 * Checks if this is classy custom template
	 * 
	 * @return boolean
	 */
	public static function is_classy_template() {

		return self::get_classy_template() ? true : false;
	}


	/**
	 * Returns classy template name or boolean if this is not classy template
	 * 
	 * @return mixed
	 */
	public static function get_classy_template() {

		$template_slug = get_page_template_slug();

		preg_match('/classy\-(.*)/', $template_slug, $matches);

		if ( $matches && isset($matches[1]) ) return $matches[1];

		return false;

	}

}