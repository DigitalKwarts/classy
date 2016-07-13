<?php
/**
 * Data that will be accessible on archive page (index).
 */
$data = array(
	'posts' => get_theme_framework()::get_posts(),
	'page_title' => get_theme_framework()::archives_title(),
	'pagination' => get_theme_framework()::get_pagination(),
);
