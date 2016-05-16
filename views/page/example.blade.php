{{-- This is an example of how page templates can be created. --}}
{{-- Template Name: Example --}}

@extends('layout.default')

@section('content')
	@if ($post)
		<article class="example">
			<h1>{{ $post->title() }}</h1>
			
			<section class="body">
				{{ $post->content() }}
			</section>
		</article>
	@endif
@stop