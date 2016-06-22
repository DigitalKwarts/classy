<?php

/**
 * Implements WordPress Hierarchy
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

		if ( is_404() ) : return '404';

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
	 * @param  string $type     view/scope
	 * @param  string $view path to view ex: "post/archive"
	 * @return string           full fule path
	 */
	public static function get_file_path( $type = 'view', $view ) {

		$view = str_replace( '.', '/', $view );

		if ( 'view' == $type ) {

			$folder = ClassyView::$folder;

			return THEME_PATH . $folder . '/' . $view . '.blade.php';

		} elseif ( 'scope' == $type ) {

			$folder = ClassyScope::$folder;

			return THEME_PATH . $folder . '/' . $view . '.php';

		}

	}


	/**
	 * Checks if view exists
	 *
	 * @param  string $type view|scope
	 * @param  string $file in blade path format, ex: layout|header
	 * @return boolean true|false
	 */
	public static function file_exists( $type = 'view', $file ) {

		$file = str_replace( '.', '/', $file );

		$file_path = self::get_file_path( $type, $file );

		return file_exists( $file_path );

	}


	/**
	 * Returns view name for render, based on type of request
	 *
	 * @param  string $type view|scope
	 * @param  string $type
	 * @return array
	 */
	public static function get_available_file( $type = 'view', $page ) {

		$views = self::get_request_hierarchy_list( $page );

		foreach ( $views as $view ) {

			if ( self::file_exists( $type, $view ) ) :

				return $view;

			endif;

		}

		return false;

	}


	/**
	 * Returns list of filenames to check, based on type of request
	 *
	 * @param  string $type
	 * @return array
	 */
	private static function get_request_hierarchy_list( $type ) {

		$views = array();

		// Home

		if ( 'home' == $type ) :

			$views[] = 'home';

			// Single

		elseif ( 'single' == $type ) :

			$post_type = get_post_type();

			$views[] = $post_type . '.single';

			$views[] = 'single';

			// Post type

		elseif ( 'post_type_archive' == $type ) :

			$post_type = get_post_type();

			$views[] = $post_type . '.archive';

			$views[] = 'archive';

			// Taxonomy

		elseif ( 'taxonomy' == $type ) :

			$term = get_queried_object();

			if ( ! empty( $term->slug ) ) {

				$taxonomy = $term->taxonomy;

				$views[] = "taxonomy.$taxonomy-{$term->slug}";
				$views[] = "taxonomy.$taxonomy";

			}

			$views[] = 'taxonomy.taxonomy';

			$views[] = 'taxonomy';

			// Category

		elseif ( 'category' == $type ) :

			$category = get_queried_object();

			if ( ! empty( $category->slug ) ) {
				$views[] = "category.{$category->slug}";
				$views[] = "category.{$category->term_id}";
			}

			$views[] = 'category.category';

			$views[] = 'category';

			// Attachment

		elseif ( 'attachment' == $type ) :

			$attachment = get_queried_object();

			if ( $attachment ) {

				if ( false !== strpos( $attachment->post_mime_type, '/' ) ) {

					list( $type, $subtype ) = explode( '/', $attachment->post_mime_type );

				} else {

					list( $type, $subtype ) = array( $attachment->post_mime_type, '' );

				}

				if ( ! empty( $subtype ) ) {
					$views[] = "attachment.{$type}.{$subtype}";
					$views[] = "attachment.{$subtype}";

					$views[] = "{$type}.{$subtype}";
					$views[] = "{$subtype}";
				}

				$views[] = "attachment.{$type}";
				$views[] = "{$type}";

			}

			$views[] = 'attachment.attachment';

			$views[] = 'attachment';

			// Tag

		elseif ( 'tag' == $type ) :

			$tag = get_queried_object();

			if ( ! empty( $tag->slug ) ) {
				$views[] = "post.tag.{$tag->slug}";
				$views[] = "post.tag.{$tag->term_id}";

				$views[] = "tag.{$tag->slug}";
				$views[] = "tag.{$tag->term_id}";
			}
			$views[] = 'post.tag';

			$views[] = 'tag';

			// Author

		elseif ( 'author' == $type ) :

			$author = get_queried_object();

			if ( $author instanceof WP_User ) {
				$views[] = "post.author.{$author->user_nicename}";
				$views[] = "post.author.{$author->ID}";

				$views[] = "author.{$author->user_nicename}";
				$views[] = "author.{$author->ID}";
			}

			$views[] = 'post.author';

			$views[] = 'author';

			// Front Page

		elseif ( 'front-page' == $type ) :

			$views[] = 'front-page.front-page';
			$views[] = 'front-page';

			$views[] = 'home.home';
			$views[] = 'home';

			$views = array_merge( $views, self::get_request_hierarchy_list( 'post_type_archive' ) );

			// Page

		elseif ( 'classy-template' == $type ) :

			$template = self::get_classy_template();

			$views[] = $template;

			$views[] = 'page.' . $template;

			$views[] = 'template.' . $template;

		elseif ( 'page' == $type ) :

			$id = get_queried_object_id();

			$pagename = get_query_var( 'pagename' );

			if ( ! $pagename && $id ) {
				// If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object
				$post = get_queried_object();

				if ( $post ) {
					$pagename = $post->post_name;
				}
			}

			if ( $pagename ) {
				$views[] = 'page.' . $pagename;
			}

			if ( $id ) {
				$views[] = 'page.' . $id;
			}

			$views[] = 'page.page';

			$views[] = 'page';

			// Default

		else :

			$views[] = $type;

		endif;

		$views[] = 'index';

		return $views;

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

		preg_match( '/classy\-(.*)/', $template_slug, $matches );

		if ( ! empty( $matches ) && isset( $matches[1] ) ) { return $matches[1]; }

		return false;

	}
}
