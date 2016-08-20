<?php
/**
 * Theme main config.
 *
 * @package Classy
 */

/**
 * Textdomain.
 * If you're translating a theme, you'll need to use a text domain to denote all text belonging to that theme.
 *
 * @link https://codex.wordpress.org/I18n_for_WordPress_Developers
 * @var string
 */
$textdomain = 'themename';

/**
 * Environment.
 * Can be development/production.
 * In this theme it is used to deliver minified assets when environment is production and originals for development.
 *
 * @var string
 */
$environment = 'production';


/**
 * Minify Html.
 * If you want to have your html minified - set this to true.
 *
 * @var boolean
 */
$minify_html = false;

/**
 * Theme Post types.
 *
 * @link https://github.com/anrw/classy/wiki/Custom-post-types
 * @var array
 */
$post_types = array();

/**
 * Theme Taxonomies.
 *
 * @link https://github.com/anrw/classy/wiki/Taxonomies
 * @var array
 */
$taxonomies = array();

/**
 * Theme post formats.
 *
 * @link https://github.com/anrw/classy/wiki/Post-formats
 * @var array
 */
$post_formats = array();

/**
 * Sidebars.
 *
 * @link https://github.com/anrw/classy/wiki/Sidebars
 * @var array
 */
$sidebars = array();


/**
 * Classy allows you to include custom modules, functions that are located in `app/custom/` directory.
 * To include them, just write here relative path like this:
 *
 * To include one file: `module/init.php`
 * To include all files from dir: `functions/*.php`
 *
 * @var array
 */
$include = array();
