<?php 

global $post;

$classypost = new ClassyPost( $post );

Classy::render( 'layout.comments', array( 'post' => $classypost ) );
