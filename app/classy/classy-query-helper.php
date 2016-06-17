<?php

class ClassyQueryHelper {

	/**
	 * Finds or creates new query based on provided params
	 * 
	 * @param  array/boolean $args
	 * @return object        WP_Query
	 */
	public static function find_query($args = false) {

		$default_args = array();

		if (!$args) {
		
			return self::get_current_query();
		
		} elseif (is_array($args)) {

			$args = array_merge($default_args, $args);

			return new WP_Query($args);

		} else {

			return new WP_Query($default_args);

		}
	}

	/**
	 * Returns current WP_Query
	 * 
	 * @return object WP_Query
	 */
	public static function get_current_query() {

        global $wp_query;

        $query =& $wp_query;

        $query = self::handle_maybe_custom_posts_page($query);

        return $query;

	}

	/**
	 * Checks and returns WP_Query for home posts page
	 * 
	 * @param  object $query WP_Query
	 * @return object        WP_Query
	 */
    private static function handle_maybe_custom_posts_page( $query ) {

    	if ($custom_posts_page = get_option('page_for_posts')) {
        
        	if ( isset($query->query['p']) && $query->query['p'] == $custom_posts_page ) {
        
        		return new WP_Query(array('post_type' => 'post'));
        
        	}
        
        }
        
        return $query;

    }

}