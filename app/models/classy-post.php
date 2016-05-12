<?php 

class ClassyPost {

	/**
	 * Current post id
	 * @var int
	 */
	public $ID;

	/**
	 * Stores current post object
	 * @var object
	 */
	protected $object;

	/**
	 * Post title
	 * @var string
	 */
	public $post_title;

	/**
	 * Post name
	 * @var string
	 */
	public $post_name;

	/**
	 * Post content (raw)
	 * @var string
	 */
	public $post_content;

	/**
	 * Post type
	 * @var string
	 */
	public $post_type;

	/**
	 * Post author id
	 * @var int
	 */
	public $post_author;

	/**
	 * Post date. String as stored in the WP database, ex: 2012-04-23 08:11:23
	 * @var string
	 */
	public $post_date;

	/**
	 * Post excerpt (raw)
	 * @var string
	 */
	public $post_excerpt;

	/**
	 * Post status. It can be draft, publish, pending, private, trash, etc.
	 * @var string
	 */
	public $post_status;

	/**
	 * Post permalink
	 * @var string
	 */
	public $permalink;

	
	/**
	 * Main constructor function. If ID won't be provided we will try to find it, based on your query
	 * @param int $pid
	 */
	public function __construct($pid = null) {
		$this->ID = $this->verify_id($pid);
		
		$this->init();
	}

	protected function verify_id($pid) {
		return $pid;
	}

	/**
	 * Initialises Instance based on provided post id
	 */
	protected function init() {
		$object = (array) $this->get_object();

		foreach ($object as $key => $value) {
			$this->$key = $value;
		}
	}

	/**
	 * Returns post object
	 * 
	 * @return object
	 */
	
	public function get_object() {
		$object = get_post($this->ID);

		return $object;
	}

	/**
	 * Checks if current user can edit this post
	 * 
	 * @return boolean
	 */
    public function can_edit() {
        if ( !function_exists( 'current_user_can' ) ) {
            return false;
        }
        if ( current_user_can( 'edit_post', $this->ID ) ) {
            return true;
        }
        return false;
    }

    /**
     * Returns the Post Edit url
     * 
     * @return string
     */
	public function get_edit_url() {
		if ( $this->can_edit() ) {
			return get_edit_post_link($this->ID);
		}
	}


	/**
	 * Returns post thumbnail
	 * 
	 * @return ClassyImage
	 */
	public function get_thumbnail() {
		if ( function_exists('get_post_thumbnail_id') ) {
			$image_id = get_post_thumbnail_id($this->ID);
			
			if ( $image_id ) {
				return new ClassyImage($image_id);
			}
		}
	}


	/**
	 * Returns post title with filters applied
	 * 
	 * @return string
	 */
	public function get_title() {
		return apply_filters('the_title', $this->post_title, $this->ID);
	}

	/**
	 * Alias for get_title
	 * 
	 * @return string
	 */
	public function title() {
		return $this->get_title();
	}


	/**
	 * Returns the post content with filters applied.
	 * 
	 * @param  integer $page Page number, in case our post has <!--nextpage--> tags
	 * @return string        Post content
	 */
	public function get_content( $page = 0 ) {
		if ( $page == 0 && $this->post_content ) {
			return $this->post_content;
		}
		
		$content = $this->post_content;
		
		if ( $page ) {
			$contents = explode('<!--nextpage-->', $content);
			
			$page--;

			if ( count($contents) > $page ) {
				$content = $contents[$page];
			}
		}

		$content = apply_filters('the_content', ($content));

		return $content;
	}


	/**
	 * Alias for get_content
	 * 
	 * @return string
	 */
	public function content() {
		return $this->get_content();
	}

	/**
	 * Returns post type object for current post
	 * 
	 * @return object
	 */
	public function get_post_type() {
		return get_post_type_object($this->post_type);
	}

	/**
	 * Returns post permalink
	 * 
	 * @return string
	 */
	public function get_permalink() {
		if ( isset($this->permalink) ) {
			return $this->permalink;
		}

		$this->permalink = get_permalink($this->ID);

		return $this->permalink;
	}


	/**
	 * Alias for get_permalink
	 * 
	 * @return string
	 */
	public function permalink() {
		return $this->get_permalink();
	}

	/**
	 * Returns post preview of requested length. 
	 * It will look for post_excerpt and will return it. 
	 * If post contains <!-- more --> tag it will return content until it
	 * 
	 * @param  integer $len      Number of words
	 * @param  boolean $force    If is set to true it will cut your post_excerpt to desired $len length
	 * @param  string  $readmore The text for 'readmore' link
	 * @param  boolean $strip    Should we strip tags?
	 * @return string            Post preview
	 */
	public function get_preview($len = 50, $force = false, $readmore = 'Read More', $strip = true) {
		$text = '';
		$trimmed = false;

		if ( isset($this->post_excerpt) && strlen($this->post_excerpt) ) {
			
			if ( $force ) {
				$text = ClassyHelper::trim_words($this->post_excerpt, $len, false);
				$trimmed = true;
			} else {
				$text = $this->post_excerpt;
			}

		}

		if ( !strlen($text) && preg_match('/<!--\s?more(.*?)?-->/', $this->post_content, $readmore_matches) ) {

			$pieces = explode($readmore_matches[0], $this->post_content);
			$text = $pieces[0];

			if ( $force ) {
				$text = ClassyHelper::trim_words($text, $len, false);
				$trimmed = true;
			}

			$text = do_shortcode( $text );

		}

		if ( !strlen($text) ) {

			$text = ClassyHelper::trim_words($this->get_content(), $len, false);
			$trimmed = true;

		}

		if ( !strlen(trim($text)) ) {

			return trim($text);

		}

		if ( $strip ) {

			$text = trim(strip_tags($text));

		}

		if ( strlen($text) ) {

			$text = trim($text);
			$last = $text[strlen($text) - 1];
			
			if ( $last != '.' && $trimmed ) {
				$text .= ' &hellip; ';
			}
			
			if ( !$strip ) {
				$last_p_tag = strrpos($text, '</p>');
				if ( $last_p_tag !== false ) {
					$text = substr($text, 0, $last_p_tag);
				}
				if ( $last != '.' && $trimmed ) {
					$text .= ' &hellip; ';
				}
			}
			
			if ( $readmore && isset($readmore_matches) && !empty($readmore_matches[1]) ) {
				$text .= ' <a href="' . $this->get_permalink() . '" class="read-more">' . trim($readmore_matches[1]) . '</a>';
			} elseif ( $readmore ) {
				$text .= ' <a href="' . $this->get_permalink() . '" class="read-more">' . trim($readmore) . '</a>';
			}
			
			if ( !$strip ) {
				$text .= '</p>';
			}

		}

		return trim($text);
	}

}