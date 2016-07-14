<?php
/**
 * Includes multiple helper function.
 *
 * @package Classy
 */

namespace Classy;

/**
 * Class Helper.
 */
class Helper {

	/**
	 * Trims text to a certain number of words.
	 *
	 * @param string $text         Text to trim.
	 * @param int    $num_words    Number of words. Default 55.
	 * @param string $more         Optional. What to append if $text needs to be trimmed. Default '&hellip;'.
	 * @param string $allowed_tags Html allowed tags.
	 *
	 * @return string Trimmed text.
	 */
	public static function trim_words( $text, $num_words = 55, $more = null, $allowed_tags = 'p a span b i br blockquote' ) {
		if ( null === $more ) {
			$more = __( '&hellip;' );
		}

		$original_text = $text;
		$allowed_tag_string = '';

		foreach ( explode( ' ', $allowed_tags ) as $tag ) {
			$allowed_tag_string .= '<' . $tag . '>';
		}

		$text = strip_tags( $text, $allowed_tag_string );

		if ( 'characters' === _x( 'words', 'word count: words or characters?' ) && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
			$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
			preg_match_all( '/./u', $text, $words_array );
			$words_array = array_slice( $words_array[0], 0, $num_words + 1 );
			$sep = '';
		} else {
			$words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
			$sep = ' ';
		}

		if ( count( $words_array ) > $num_words ) {
			array_pop( $words_array );
			$text = implode( $sep, $words_array );
			$text = $text . $more;
		} else {
			$text = implode( $sep, $words_array );
		}

		$text = self::close_tags( $text );

		return apply_filters( 'wp_trim_words', $text, $num_words, $more, $original_text );
	}

	/**
	 * Close tags in html code.
	 *
	 * @param string $html Html code to be checked.
	 *
	 * @return string
	 */
	public static function close_tags( $html ) {
		// Put all opened tags into an array.
		preg_match_all( '#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result );

		$openedtags = $result[1];

		// Put all closed tags into an array.
		preg_match_all( '#</([a-z]+)>#iU', $html, $result );

		$closedtags = $result[1];
		$len_opened = count( $openedtags );

		// All tags are closed.
		if ( count( $closedtags ) === $len_opened ) {
			return $html;
		}

		$openedtags = array_reverse( $openedtags );

		// Close tags.
		for ( $i = 0; $i < $len_opened; $i++ ) {
			if ( ! in_array( $openedtags[ $i ], $closedtags, true ) ) {
				$html .= '</' . $openedtags[ $i ] . '>';
			} else {
				unset( $closedtags[ array_search( $openedtags[ $i ], $closedtags ) ] );
			}
		}

		$html = str_replace( array( '</br>', '</hr>', '</wbr>' ), '', $html );
		$html = str_replace( array( '<br>', '<hr>', '<wbr>' ), array( '<br />', '<hr />', '<wbr />' ), $html );

		return $html;
	}

	/**
	 * Retrieve paginated link for archive post pages.
	 *
	 * @param string|array $args Optional. Array or string of arguments for generating paginated links for archives.
	 *
	 * @return array
	 */
	public static function paginate_links( $args = '' ) {
		$defaults = array(
			'base' => '%_%', // Example http://example.com/all_posts.php%_% : %_% is replaced by format (below).
			'format' => '?page=%#%', // Example ?page=%#% : %#% is replaced by the page number.
			'total' => 1,
			'current' => 0,
			'show_all' => false,
			'prev_next' => true,
			'prev_text' => __( '&laquo; Previous' ),
			'next_text' => __( 'Next &raquo;' ),
			'end_size' => 1,
			'mid_size' => 2,
			'type' => 'array',
			'add_args' => false, // Array of query args to add.
			'add_fragment' => '',
		);
		$args = wp_parse_args( $args, $defaults );

		// Who knows what else people pass in $args.
		$args['total'] = intval( (int) $args['total'] );
		if ( $args['total'] < 2 ) {
			return array();
		}
		$args['current'] = (int) $args['current'];
		$args['end_size'] = 0 < (int) $args['end_size'] ? (int) $args['end_size'] : 1; // Out of bounds?  Make it the default.
		$args['mid_size'] = 0 <= (int) $args['mid_size'] ? (int) $args['mid_size'] : 2;
		$args['add_args'] = is_array( $args['add_args'] ) ? $args['add_args'] : false;
		$page_links = array();
		$dots = false;
		if ( $args['prev_next'] && $args['current'] && 1 < $args['current'] ) {
			$link = str_replace( '%_%', 2 === absint( $args['current'] ) ? '' : $args['format'], $args['base'] );
			$link = str_replace( '%#%', $args['current'] - 1, $link );
			if ( $args['add_args'] ) {
				$link = add_query_arg( $args['add_args'], $link );
			}
			$link .= $args['add_fragment'];
			$link = untrailingslashit( $link );
			$page_links[] = array(
				'class' => 'prev page-numbers',
				'link' => esc_url( apply_filters( 'paginate_links', $link ) ),
				'title' => $args['prev_text'],
			);
		}
		for ( $n = 1; $n <= $args['total']; $n++ ) {
			$n_display = number_format_i18n( $n );
			if ( absint( $args['current'] ) === $n ) {
				$page_links[] = array(
					'class' => 'page-number page-numbers current',
					'title' => $n_display,
					'text' => $n_display,
					'name' => $n_display,
					'current' => true,
				);
				$dots = true;
			} else {
				if ( $args['show_all'] || ( $n <= $args['end_size'] || ( $args['current'] && $n >= $args['current'] - $args['mid_size'] && $n <= $args['current'] + $args['mid_size'] ) || $n > $args['total'] - $args['end_size'] ) ) {
					$link = str_replace( '%_%', 1 === absint( $n ) ? '' : $args['format'], $args['base'] );
					$link = str_replace( '%#%', $n, $link );
					$link = trailingslashit( $link ) . ltrim( $args['add_fragment'], '/' );
					if ( $args['add_args'] ) {
						$link = rtrim( add_query_arg( $args['add_args'], $link ), '/' );
					}
					$link = str_replace( ' ', '+', $link );
					$link = untrailingslashit( $link );
					$page_links[] = array(
						'class' => 'page-number page-numbers',
						'link' => esc_url( apply_filters( 'paginate_links', $link ) ),
						'title' => $n_display,
						'name' => $n_display,
						'current' => absint( $args['current'] ) === $n,
					);
					$dots = true;
				} elseif ( $dots && ! $args['show_all'] ) {
					$page_links[] = array(
						'class' => 'dots',
						'title' => __( '&hellip;' ),
					);
					$dots = false;
				}
			}
		}
		if ( $args['prev_next'] && $args['current'] && ( $args['current'] < $args['total'] || -1 === intval( $args['total'] ) ) ) {
			$link = str_replace( '%_%', $args['format'], $args['base'] );
			$link = str_replace( '%#%', $args['current'] + 1, $link );
			if ( $args['add_args'] ) {
				$link = add_query_arg( $args['add_args'], $link );
			}
			$link = untrailingslashit( trailingslashit( $link ) . $args['add_fragment'] );
			$page_links[] = array(
				'class' => 'next page-numbers',
				'link' => esc_url( apply_filters( 'paginate_links', $link ) ),
				'title' => $args['next_text'],
			);
		}
		return $page_links;
	}

	/**
	 * Converts array to object recursively.
	 *
	 * @param array $array Array to be converted.
	 *
	 * @return object
	 */
	public static function array_to_object( $array ) {
		$obj = new \stdClass;

		foreach ( $array as $k => $v ) {
			if ( strlen( $k ) ) {
				if ( is_array( $v ) ) {
					$obj->{$k} = self::array_to_object( $v ); // Recursion.
				} else {
					$obj->{$k} = $v;
				}
			}
		}

		return $obj;
	}

	/**
	 * Returns Current Archives Page Title.
	 *
	 * @return string
	 */
	public static function get_archives_title() {
		$textdomain = Classy::textdomain();
		$archives_title = 'Archives';

	    if ( is_category() ) {
	        $archives_title = single_cat_title( '', false );
	    } else if ( is_tag() ) {
	        $archives_title = 'Tag: ' . single_tag_title( '', false );
	    } else if ( is_author() ) {
	        if ( have_posts() ) {
	            the_post();
	            $archives_title = 'Author: ' . get_the_author();
	        }

	        rewind_posts();
	    } else if ( is_search() ) {
	        $archives_title = sprintf( __( 'Search Results for: %s', $textdomain ), '<span>' . get_search_query() . '</span>' );
	    } else if ( is_archive() ) {
	        if ( is_day() ) {
	            $archives_title = get_the_date();
	        } elseif ( is_month() ) {
	            $archives_title = get_the_date( _x( 'F Y', 'monthly archives date format', $textdomain ) );
	        } elseif ( is_year() ) {
	            $archives_title = get_the_date( _x( 'Y', 'yearly archives date format', $textdomain ) );
	        }
		}

	    return $archives_title;
	}
}
