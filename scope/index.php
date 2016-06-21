<?php

/**
 * Data that will be accesible on archive page (index)
 */

$data = array(
	'posts' => Classy::get_posts(),
	'page_title' => Classy::archives_title(),
	'pagination' => Classy::get_pagination(),
);
