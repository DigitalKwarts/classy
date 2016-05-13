<header>
	<nav>
		<ul>
			@if ($items = $menu->get_items())
				@foreach ($items as $item)
					<li><a href="{{ $item->get_link() }}">{{ $item->get_title() }}</a></li>
				@endforeach
			@endif
		</ul>
	</nav>
</header>