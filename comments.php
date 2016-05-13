<?php 

global $post;

$classypost = new ClassyPost($post->ID);

Classy::render('base.comments', array('post' => $classypost));
