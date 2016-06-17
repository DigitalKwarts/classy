<?php

class ClassyUser extends ClassyBasis {

	/**
	 * Current user id
	 * @var int
	 */
	public $ID;

	/**
	 * User posts url
	 * @var string
	 */
	public $link;

	/**
	 * User login (example: anrw)
	 * @var string
	 */
	public $user_login;

	/**
	 * User full name
	 * @var string
	 */
	public $name;

	/**
	 * It's basically sanitized version of user login, used for permalinks
	 * @var string
	 */
	public $user_nicename;

	/**
	 * User email
	 * @var string
	 */
	public $user_email;

	/**
	 * Human-Friendly name, like: Andrew Tolochka
	 * @var string
	 */
	public $display_name;

	/**
	 * User password hash
	 * @var string
	 */
	private $user_pass;

	/**
	 * Stores current post object
	 * @var object
	 */
	private $object;

	/**
	 * Main constructor function. Requires user id
	 * @param int $uid
	 */
	public function __construct( $uid = null ) {
		$this->ID = $this->verify_id( $uid );

		$this->init();
	}

	private function verify_id( $uid ) {
		return $uid;
	}

	/**
	 * Initialises Instance based on provided post id
	 */
	private function init() {
		$object = (array) $this->get_object();

		$this->import( $object );

		if ( isset( $this->first_name ) && isset( $this->last_name ) ) {
			$this->name = $this->first_name . ' ' . $this->last_name;
		} else {
			$this->name = 'Anonymous';
		}

	}

	/**
	 * Returns user object
	 *
	 * @return object
	 */
	private function get_object() {
		return get_userdata( $this->ID );
	}

	/**
	 * Returns user first name
	 *
	 * @return string
	 */
	public function first_name() {

		return $this->first_name;

	}

	/**
	 * Returns user display name
	 *
	 * @return string
	 */
	public function display_name() {

		return $this->display_name;

	}

	/**
	 * Returns user full name
	 *
	 * @return string
	 */
	public function name() {

		return $this->name;

	}

	/**
	 * Returns user user_login
	 *
	 * @return string
	 */
	public function user_login() {

		return $this->user_login;

	}

	/**
	 * Returns user email
	 *
	 * @return string
	 */
	public function email() {

		return $this->user_email;

	}

	/**
	 * Returns author posts url
	 *
	 * @return string
	 */
	public function link() {
		if ( ! $this->link ) {
			$this->link = get_author_posts_url( $this->ID );
		}

		return $this->link;
	}
}
