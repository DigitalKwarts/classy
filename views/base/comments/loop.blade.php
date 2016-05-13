@if ($comments = $post->get_comments())
	<h4>Comments ({{ count($comments) }}):</h4>
	<ul>
		@foreach ($comments as $comment)
			<li>
				<img src="{{ $comment->avatar() }}" alt="">
				<h4>{{ $comment->author()->name() }} on {{ $comment->date() }}</h4>
				<div class="comment">{{ $comment->content() }}</div>
			</li>
		@endforeach
	</ul>
@else
	<p>No comments</p>
@endif