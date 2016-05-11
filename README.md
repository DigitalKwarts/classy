![alt tag](http://i.imgur.com/2TgPJNk.png)

Classy is a framework for building wordpress themes, based on [Blade](https://laravel.com/docs/5.1/blade) template engine. It's fast with beautiful architecture that allows you to write less code and focus more on project itself. It doesn't provide frontend boilerplate, since every project needs its own, instead it handles all architecture, providing an elegant way to separate logic from view.

### Why Blade?

Blade is the simple, yet powerful templating engine provided with Laravel. Unlike other popular PHP templating engines, Blade does not restrict you from using plain PHP code in your views. All Blade views are compiled into plain PHP code and cached until they are modified, meaning Blade adds essentially zero overhead to your application.

##### Code example:

```blade
@extends('basic')

@section('content')
	<h1 class="big-title">{{ $foo }}</h1>
	<h2 class="post-title">{{ $post.title() }}</h2>
	<img src="{{ $post.thumbnail('large') }}" />
	<div class="body">
		{{ post.content() }}
	</div>
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

## Documentation
1. [Custom post types](https://github.com/anrw/classy/wiki/Custom-post-types)
2. [Taxonomies](https://github.com/anrw/classy/wiki/Taxonomies)
3. [Post formats](https://github.com/anrw/classy/wiki/Post-formats)
4. [Sidebars](https://github.com/anrw/classy/wiki/Sidebars)
5. [Blade](https://laravel.com/docs/5.1/blade)

## Requirements:

Wordpress: 4.5+
PHP: 5.4+