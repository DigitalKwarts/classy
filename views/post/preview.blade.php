<article>
	<figure>
		<img src="{{ $post->thumbnail()->src('medium') }}" alt="">
	</figure>
	<h3><a href="{{ $post->permalink() }}">{{ $post->title() }}</a></h3>
</article>