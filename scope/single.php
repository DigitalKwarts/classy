<?php
/**
 * Data that will be accessible on single post.
 */
$framework = get_theme_framework();
$data = array(
	'post' => $framework::get_post(),
);
