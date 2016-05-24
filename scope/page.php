<?php 

/**
 * Data that will be accesible on every page view. In case some other templates don't overwrite it with their own scope.
 */

$data = array(
	'post' => Classy::get_post(),
);