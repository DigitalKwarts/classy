<?php 

global $post;

$classypost = new \Classy\Models\Post( $post );

Classy\Classy::render( 'layout.comments', array( 'post' => $classypost ) );
