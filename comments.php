<?php 

global $post;

$classypost = new ClassyPost($post->ID);

Classy::render('layout.comments', array('post' => $classypost));
