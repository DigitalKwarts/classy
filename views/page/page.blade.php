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