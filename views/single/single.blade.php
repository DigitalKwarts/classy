@extends('basic')

@section('content')
	<article>
		<h1>{{ $post->title() }}</h1>
		<section>
			{{ $post->content() }}
		</section>
	</article>
@stop