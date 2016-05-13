@extends('base.default')

@section('content')
	<h1>{{ $page_title }}</h1>

	@forelse ($posts as $post)
		<article>
			<h3><a href="{{ $post->permalink() }}">{{ $post->title() }}</a></h3>
		</article>
	@empty
		<p>No posts</p>
	@endforelse

	@if ($pagination)
		<div class="pagination">
			@if (isset($pagination->prev))
				<a href="{{ $pagination->prev->link }}" class="prev {{ $pagination->prev->link }}">Prev</a>
			@endif

			<ul class="pages">
				@foreach ($pagination->pages as $page)
					<li>
						@if (isset($page->link))
							<a href="{{ $page->link }}" class="{{ $page->class }}">{{ $page->title }}</a>
						@else
							<span class="{{ $page->class }}">{{ $page->title }}</span>
						@endif
					</li>
				@endforeach
			</ul>

			@if (isset($pagination->next))
				<a href="{{ $pagination->next->link }}" class="next {{ $pagination->next->link}}">Next</a>
			@endif
		</div>
	@endif
	
@stop