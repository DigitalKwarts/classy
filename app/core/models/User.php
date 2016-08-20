<?php
/**
 * Wrapper for \WP_User.
 *
 * @package Classy\Models
 */

namespace Classy\Models;

use Classy\Basis;

/**
 * Class User.
 */
class User extends Basis {

	/**
	 * Current user id.
	 *
	 * @var int
	 */
	public $ID;

	/**
	 * User posts url.
	 *
	 * @var string
	 */
	public $link;

	/**
	 * User login (example: anrw).
	 *
	 * @var string
	 */
	public $user_login;

	/**
	 * User full name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * It's basically sanitized version of user login, used for permalinks.
	 *
	 * @var string
	 */
	public $user_nicename;

	/**
	 * User email.
	 *
	 * @var string
	 */
	public $user_email;

	/**
	 * Human-Friendly name, like: Andrew Tolochka.
	 *
	 * @var string
	 */
	public $display_name;

	/**
	 * Stores current user object.
	 *
	 * @var object
	 */
	private $object;

	/**
	 * Main constructor function. Requires user id.
	 *
	 * @param int $uid User id.
	 */
	public function __construct( $uid = null ) {
		$this->ID = $this->verify_id( $uid );

		$this->init();
	}

	/**
	 * Verify user id.
	 *
	 * @param int $uid User id.
	 *
	 * @return int
	 */
	private function verify_id( $uid ) {
		// @todo: Realize this method.
		return $uid;
	}

	/**
	 * Initialises User Instance.
	 */
	private function init() {
		$this->object = $this->get_object();

		$this->import( $this->object );

		$this->setup_user_name();
	}

	/**
	 * Returns user object.
	 *
	 * @return object
	 */
	private function get_object() {
		return get_user_by( 'id', $this->ID );
	}

	/**
	 * Returns user first name.
	 *
	 * @return string
	 */
	public function first_name() {
		return $this->object->first_name;
	}

	/**
	 * Returns user display name.
	 *
	 * @return string
	 */
	public function display_name() {
		return $this->display_name;
	}

	/**
	 * Returns user full name.
	 *
	 * @return string
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * Returns user user_login.
	 *
	 * @return string
	 */
	public function user_login() {
		return $this->user_login;
	}

	/**
	 * Returns user email.
	 *
	 * @return string
	 */
	public function email() {
		return $this->user_email;
	}

	/**
	 * Returns author posts url.
	 *
	 * @return string
	 */
	public function link() {
		if ( ! $this->link ) {
			$this->link = get_author_posts_url( $this->ID );
		}

		return $this->link;
	}

	/**
	 * Setup user name and display name.
	 */
	private function setup_user_name() {
		$this->name = 'Anonymous';
		if ( isset( $this->object->first_name ) && isset( $this->object->last_name ) ) {
			$this->name = $this->object->first_name . ' ' . $this->object->last_name;
		}
	}
}
