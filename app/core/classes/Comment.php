<?php
/**
 * Wrapper and helper for WP_Comment class.
 *
 * @package Classy
 */

namespace Classy;

/**
 * Class Comment.
 */
class Comment extends Basis {

	/**
	 * Comment ID.
	 *
	 * @var int
	 */
	public $comment_ID;

	/**
	 * Comment post ID.
	 *
	 * @var int
	 */
	public $comment_post_ID;

	/**
	 * Comment Author name.
	 *
	 * @var string
	 */
	public $comment_author;

	/**
	 * Comment author email.
	 *
	 * @var string
	 */
	public $comment_author_email;

	/**
	 * Comment author link.
	 *
	 * @var string
	 */
	public $comment_author_url;

	/**
	 * Comment.
	 *
	 * @var string
	 */
	public $comment_content;

	/**
	 * Comment approved.
	 *
	 * @var boolean
	 */
	public $comment_approved;

	/**
	 * Comment date.
	 *
	 * @var string
	 */
	public $comment_date;

	/**
	 * User ID.
	 *
	 * @var int
	 */
	public $user_id;

	/**
	 * Comment nested level.
	 *
	 * @var int
	 */
	public $level;

	/**
	 * Comment Parent ID.
	 *
	 * @var int
	 */
	public $comment_parent;

	/**
	 * Child comments.
	 *
	 * @var array
	 */
	protected $children = array();

	/**
	 * Checks if provided arg is instance of WP_Comment and init it.
	 *
	 * @param \WP_Comment $item WP_Comment object.
	 */
	public function __construct( $item ) {
		if ( is_a( $item, '\WP_Comment' ) ) {
			$this->import( $item );
		}
	}

	/**
	 * Returns User object of comment author.
	 *
	 * @return \Classy\Models\User
	 */
	public function author() {
		if ( ! $this->user_id ) {

			$author = new Models\User( 0 );

			if ( isset( $this->comment_author ) && $this->comment_author ) {
				$author->name = $this->comment_author;
			}

			return $author;
		}

		return new Models\User( $this->user_id );
	}

	/**
	 * Returns comment content.
	 *
	 * @return string
	 */
	public function content() {
		return apply_filters( 'get_comment_text ', $this->comment_content );
	}

	/**
	 * Return true if comment is approved.
	 *
	 * @return boolean
	 */
	public function approved() {
		return $this->comment_approved;
	}

	/**
	 * Returns comment date.
	 *
	 * @param string $date_format Optional. PHP date format defaults to the date_format option if not specified.
	 *
	 * @return string
	 */
	public function date( $date_format = '' ) {
		$df = $date_format ? $date_format : get_option( 'date_format' );
		$the_date = (string) mysql2date( $df, $this->comment_date );

		return apply_filters( 'get_comment_date ', $the_date, $df );
	}

	/**
	 * Returns true if comment has parent.
	 *
	 * @return boolean
	 */
	public function has_parent() {
		return $this->comment_parent > 0;
	}

	/**
	 * Shows gravatar.
	 *
	 * @param integer $size    avatar image size
	 * @param string  $default
	 *
	 * @return string
	 */
	public function avatar( $size = 92, $default = '' ) {
		if ( ! get_option( 'show_avatars' ) ) {
			return false;
		}

		if ( ! is_numeric( $size ) ) { $size = '92'; }

		$email = $this->get_avatar_email();
		$email_hash = '';

		if ( ! empty( $email ) ) {
			$email_hash = md5( strtolower( trim( $email ) ) );
		}

		$host = $this->get_avatar_host( $email_hash );
		$default = $this->get_default_avatar( $default, $email, $size, $host );

		if ( ! empty( $email ) ) {
			$avatar = $this->get_avatar_url( $default, $host, $email_hash, $size );
		} else {
			$avatar = $default;
		}

		return $avatar;
	}

	/**
	 * Returns email address that will be used for generating avatar.
	 *
	 * @return string
	 */
	protected function get_avatar_email() {
		$id = (int) $this->user_id;
		$user = get_userdata( $id );

		if ( $user ) {
			$email = $user->user_email;
		} else {
			$email = $this->comment_author_email;
		}

		return $email;
	}

	/**
	 * Returns default avatar url.
	 *
	 * @param  string $default
	 * @param  string $email
	 * @param  int $size
	 * @param  string $host
	 * @return string
	 */
	protected function get_default_avatar( $default, $email, $size, $host ) {
		if ( '/' === substr( $default, 0, 1 ) ) {
			$default = home_url() . $default;
		}

		if ( empty( $default ) ) {
			$avatar_default = get_option( 'avatar_default' );
			if ( empty( $avatar_default ) ) {
				$default = 'mystery';
			} else {
				$default = $avatar_default;
			}
		}

		if ( 'mystery' === $default ) {
			$default = $host . '/avatar/ad516503a11cd5ca435acc9bb6523536?s=' . $size;
			// ad516503a11cd5ca435acc9bb6523536 == md5('unknown@gravatar.com')
		} else if ( 'blank' === $default ) {
			$default = $email ? 'blank' : includes_url( 'images/blank.gif' );
		} else if ( ! empty( $email ) && 'gravatar_default' === $default ) {
			$default = '';
		} else if ( 'gravatar_default' === $default ) {
			$default = $host . '/avatar/?s=' . $size;
		} else if ( empty( $email ) && ! strstr( $default, 'http://' ) ) {
			$default = $host . '/avatar/?d=' . $default . '&amp;s=' . $size;
		}

		return $default;
	}

	/**
	 * Returns gravatar host.
	 *
	 * @param  string $email_hash
	 * @return string
	 */
	protected function get_avatar_host( $email_hash ) {

		if ( is_ssl() ) {
			$host = 'https://secure.gravatar.com';
		} else {
			if ( ! empty( $email_hash ) ) {
				$host = sprintf( 'http://%d.gravatar.com', (hexdec( $email_hash[0] ) % 2) );
			} else {
				$host = 'http://0.gravatar.com';
			}
		}

		return $host;
	}

	/**
	 * Returns avatar url
	 *
	 * @param  string $default
	 * @param  string $host
	 * @param  string $email_hash
	 * @param  int $size
	 * @return string
	 */
	protected function get_avatar_url( $default, $host, $email_hash, $size ) {
		$_return = $host . '/avatar/' . $email_hash . '?s=' . $size . '&amp;d=' . urlencode( $default );
		$rating = get_option( 'avatar_rating' );

		if ( ! empty( $rating ) ) {
			$_return .= '&amp;r=' . $rating;
		}

		return str_replace( '&#038;', '&amp;', esc_url( $_return ) );
	}

	/**
	 * Adds child to current Comment
	 *
	 * @param Comment $comment
	 */
	public function add_child( $comment ) {
		$this->children[] = $comment;
		$item->level = $this->level + 1;

		if ( $item->children ) {
			$this->update_child_levels();
		}
	}

	/**
	 * Updates children nesting level param
	 *
	 * @return boolean
	 */
	protected function update_child_levels() {
		if ( is_array( $this->children ) ) {
			foreach ( $this->children as $child ) {
				$child->level = $this->level + 1;
				$child->update_child_levels();
			}

			return true;
		}

		return false;
	}
}
