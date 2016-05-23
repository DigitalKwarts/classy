![alt tag](http://i.imgur.com/2TgPJNk.png)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/anrw/classy/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/anrw/classy/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/anrw/classy/badges/build.png?b=master)](https://scrutinizer-ci.com/g/anrw/classy/build-status/master)


Classy is a framework for building wordpress themes, based on [Blade](https://laravel.com/docs/5.1/blade) template engine. It's fast with beautiful architecture that allows you to write less code and focus more on project itself. It doesn't provide frontend boilerplate, since every project needs its own, instead it handles all architecture, providing an elegant way to separate logic from view.

### Why Blade?

Blade is the simple, yet powerful templating engine provided with Laravel. Unlike other popular PHP templating engines, Blade does not restrict you from using plain PHP code in your views. All Blade views are compiled into plain PHP code and cached until they are modified, meaning Blade adds essentially zero overhead to your application.

##### Code example:

```html
@extends('layout.default')

@section('content')
	@if ($post)
		<article>
			<h1>{{ $post->title() }}</h1>
			
			<section class="body">
				{{ $post->content() }}
			</section>
		</article>
	@endif
@stop
```

### What’s about structure?

The biggest problem with wordpress theme development is that you always need to repeat same code.  New approach, that assumes that template data will be collected and prepared separately from actual render allows you to have the project structured more accurate.

![alt tag](http://i.imgur.com/u28abeN.png)


### How it works?

`ClassyTemplate` repeats the wordpress template hierarchy and uses it separately for scopes and templates. This allows to use the same scope with different templates and different scopes with the same template.

## Example of project structure
![alt tag](http://i.imgur.com/7BUl5lR.png)


## Getting started
1. Navigate to your WordPress themes directory `cd ~/Sites/mysite/wp-content/themes`
2. Clone repository `git clone git@github.com:anrw/classy.git`
3. Navigate to it `cd classy`
4. Install composer dependencies `composer install`

## Config file
Theme config file is located in `app/config.php`. This is a place where you can easily registes custom post types, taxonomies, post formats, set up textdomain, current environment and much more. We recommend you to have is initialised this way, however this is not strict rule and everything is up to you :)

#### Custom post types

`$post_types` is a simple array. To insert a custom post type just add a new value like:

```php
$post_types = array(
	'gallery' => array(
		'config' => array(
			'public' => true,
			'exclude_from_search' => true,
			'menu_position' => 20,
			'has_archive'   => true,
			'supports'=> array(
				'title',
				'editor',
				'page-attributes',
				),
			'show_in_nav_menus' => true,
			),
		'singular' => 'Gallery',
		'multiple' => 'Galleries',
	)
)
```

The structure is the same as for `register_post_type` function, described in `https://codex.wordpress.org/Post_Formats` with one exception that we have added `Singular` and `Multiple` keys to generate all required labels automatically.

#### Post formats

To add a post format, you need to insert its name in `$post_formats` array. 

```php
$post_formats = array(
	'aside', 
	'gallery', 
	'link'
);
```

For more reference please visit: https://codex.wordpress.org/Post_Formats


#### Taxonomies

Similar to `$post_types`, `$taxonomies` is an array. To insert a taxonomy just add a new value like:

```php
$taxonomies = array(
	'gallery-category'    => array(
		'for'        => array('gallery'),
		'config'    => array(
			'sort'        => true,
			'args'        => array('orderby' => 'term_order'),
			'hierarchical' => true,
			),
		'singular'    => 'Category',
		'multiple'    => 'Categories',
	)
)
```

The structure is the same as for `register_taxonomy` function, described in `https://codex.wordpress.org/Taxonomies` with one exception that we have added `Singular` and `Multiple` keys to generate all required labels automatically.

#### Sidebars

Find `$sidebars` variable. The array key means sidebar id, but the value is its title
```php
$sidebars = array(
	'general-sidebar' => 'General Sidebar'
);
```

Here what is under the hood:
```php
foreach ( $sidebars as $id => $title ) {
	register_sidebar(
		array(
			'id' => $id,
			'name' => __($title, $domain),
			'description' => __($title, $domain),
			'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-inner">',
			'after_widget' => '</div></div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>'
		)
	);
}
```

## Page Templates

To register a template you need simply to put `{{-- Template Name: Example --}}` at the top of your blade file. 
We recommend you to structure your templates in one of this ways:

1. views/`page`/`template-name`.blade.php
2. views/`templates`/`template-name`.blade.php
3. views/`template-name`/`template-name`.blade.php

## Useful links
1. [Blade documentation](https://laravel.com/docs/5.1/blade)

## Requirements:

Wordpress: 4.5+
PHP: 5.4+