<?php
/**
 * Data that will be accessible on pages with "Example" template.
 */
$framework = get_theme_framework();
$data = array(
	'post' => $framework::get_post(),
);
