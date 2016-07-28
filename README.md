![alt tag](http://i.imgur.com/2TgPJNk.png)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/anrw/classy/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/anrw/classy/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/anrw/classy/badges/build.png?b=master)](https://scrutinizer-ci.com/g/anrw/classy/build-status/master)


Classy is a framework for building WordPress themes, based on [Blade](https://laravel.com/docs/5.1/blade) template engine. It's fast with beautiful architecture that allows you to write less code and focus more on project itself. It doesn't provide frontend boilerplate, since every project needs its own, instead it handles all architecture, providing an elegant way to separate logic from view.

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

The biggest problem with WordPress theme development is that you always need to repeat same code.  New approach, that assumes that template data will be collected and prepared separately from actual render allows you to have the project structured more accurate.

![alt tag](http://i.imgur.com/u28abeN.png)


### How it works?

`ClassyView` and `ClassyScope` repeat the WordPress template hierarchy, however they do it independently. This allows to use the same scope with different templates and different scopes with the same template.

## Example of project structure
![alt tag](http://i.imgur.com/7BUl5lR.png)


## Getting started
1. Navigate to your WordPress themes directory `cd ~/Sites/mysite/wp-content/themes`
2. Clone repository `git clone git@github.com:anrw/classy.git`
3. Navigate to it `cd classy`
4. Install composer dependencies `composer install`

## Config file
Check our GitHub [Wiki](https://github.com/anrw/classy/wiki)

## Page Templates

To register a template you need simply to put `{{-- Template Name: Example --}}` at the top of your blade file. 
We recommend you to structure your templates in one of this ways:

1. views/`page`/`templatename`.blade.php
2. views/`template`/`templatename`.blade.php
3. views/`template-name`/`templatename`.blade.php

## Useful links
1. [Blade documentation](https://laravel.com/docs/5.1/blade)
2. [WordPress Code Style](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/)
2. [PHP Mess Detector](https://phpmd.org/)

## Requirements:

* WordPress: 4.5+
* PHP: 5.6+

## How to check php code style

Check WP Code Style `composer cs`

## How to run static code analyzer
 
Check PHPMD `composer md`

## Contribute

You can report a bug, implement a feature or simply give an idea on how to make the project better. Every input from your side will be highly appreciated!

## Big thanks to:

[<img alt="anrw" src="https://avatars.githubusercontent.com/u/7533603?v=3&s=117" width="117">](https://github.com/anrw) |[<img alt="LehaMotovilov" src="https://avatars.githubusercontent.com/u/6247404?v=3&s=117" width="117">](https://github.com/LehaMotovilov) |[<img alt="konstantp" src="https://avatars.githubusercontent.com/u/8895125?v=3&s=117" width="117">](https://github.com/konstantp) |
:---: |:---: |:---: |
[anrw](https://github.com/anrw) |[LehaMotovilov](https://github.com/LehaMotovilov) |[konstantp](https://github.com/konstantp) |

## License

Classy is under GNU General Public Licence (GPL). You can use it in your personal and commercial work.
